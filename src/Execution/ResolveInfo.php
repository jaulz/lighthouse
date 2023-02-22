<?php

namespace Nuwave\Lighthouse\Execution;

use GraphQL\Type\Definition\ResolveInfo as BaseResolveInfo;
use Illuminate\Support\Collection;
use Nuwave\Lighthouse\Execution\Arguments\ArgumentSet;
use Nuwave\Lighthouse\Scout\ScoutEnhancer;
use Nuwave\Lighthouse\Support\Contracts\ArgBuilderDirective;
use Nuwave\Lighthouse\Support\Contracts\FieldBuilderDirective;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Support\Utils;

class ResolveInfo extends BaseResolveInfo
{
    public function __construct(
        BaseResolveInfo $baseResolveInfo,
        public ArgumentSet $argumentSet
    ) {
        parent::__construct(
            fieldDefinition: $baseResolveInfo->fieldDefinition,
            fieldNodes: $baseResolveInfo->fieldNodes,
            parentType: $baseResolveInfo->parentType,
            path: $baseResolveInfo->path,
            schema: $baseResolveInfo->schema,
            fragments: $baseResolveInfo->fragments,
            rootValue: $baseResolveInfo->rootValue,
            operation: $baseResolveInfo->operation,
            variableValues: $baseResolveInfo->variableValues
        );
    }

    /**
     * Apply ArgBuilderDirectives and scopes to the builder.
     *
     * @param  \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Laravel\Scout\Builder  $builder
     * @param  array<string>  $scopes
     * @param  array<string, mixed>  $args
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Laravel\Scout\Builder
     */
    public function enhanceBuilder(object $builder, array $scopes, $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo, \Closure $directiveFilter = null): object
    {
        $argumentSet = $resolveInfo->argumentSet;

        $scoutEnhancer = new ScoutEnhancer($argumentSet, $builder);
        if ($scoutEnhancer->canEnhanceBuilder()) {
            return $scoutEnhancer->enhanceBuilder();
        }

        self::applyArgBuilderDirectives($argumentSet, $builder, $directiveFilter);
        self::applyFieldBuilderDirectives($builder, $root, $args, $context, $resolveInfo);

        foreach ($scopes as $scope) {
            $builder->{$scope}($args);
        }

        return $builder;
    }

    /**
     * Would the builder be enhanced in any way?
     *
     * @param  \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Laravel\Scout\Builder  $builder
     * @param  array<string>  $scopes
     * @param  array<string, mixed>  $args
     */
    public function wouldEnhanceBuilder(object $builder, array $scopes, $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo, \Closure $directiveFilter = null): bool
    {
        $argumentSet = $resolveInfo->argumentSet;

        return (new ScoutEnhancer($argumentSet, $builder))->wouldEnhanceBuilder()
            || self::wouldApplyArgBuilderDirectives($argumentSet, $builder, $directiveFilter)
            || self::wouldApplyFieldBuilderDirectives($resolveInfo)
            || count($scopes) > 0;
    }

    /**
     * Recursively apply the ArgBuilderDirectives onto the builder.
     *
     * @param  \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder  $builder
     * @param  (\Closure(\Nuwave\Lighthouse\Support\Contracts\ArgBuilderDirective): bool)|null  $directiveFilter
     */
    protected static function applyArgBuilderDirectives(ArgumentSet $argumentSet, object &$builder, \Closure $directiveFilter = null): void
    {
        foreach ($argumentSet->arguments as $argument) {
            $value = $argument->toPlain();

            $filteredDirectives = $argument
                ->directives
                ->filter(Utils::instanceofMatcher(ArgBuilderDirective::class));

            if (null !== $directiveFilter) {
                $filteredDirectives = $filteredDirectives->filter($directiveFilter);
            }

            $filteredDirectives->each(static function (ArgBuilderDirective $argBuilderDirective) use (&$builder, $value): void {
                $builder = $argBuilderDirective->handleBuilder($builder, $value);
            });

            Utils::applyEach(
                static function ($value) use (&$builder, $directiveFilter) {
                    if ($value instanceof ArgumentSet) {
                        self::applyArgBuilderDirectives($value, $builder, $directiveFilter);
                    }
                },
                $argument->value
            );
        }
    }

    /**
     * Would there be any ArgBuilderDirectives to apply to the builder?
     *
     * @param  \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder  $builder
     * @param  (\Closure(\Nuwave\Lighthouse\Support\Contracts\ArgBuilderDirective): bool)|null  $directiveFilter
     */
    protected static function wouldApplyArgBuilderDirectives(ArgumentSet $argumentSet, object &$builder, \Closure $directiveFilter = null): bool
    {
        foreach ($argumentSet->arguments as $argument) {
            $filteredDirectives = $argument
                ->directives
                ->filter(Utils::instanceofMatcher(ArgBuilderDirective::class));

            if (null !== $directiveFilter) {
                $filteredDirectives = $filteredDirectives->filter($directiveFilter);
            }

            if ($filteredDirectives->isNotEmpty()) {
                return true;
            }

            $valueOrValues = $argument->value;
            if ($valueOrValues instanceof ArgumentSet) {
                return self::wouldApplyArgBuilderDirectives($valueOrValues, $builder, $directiveFilter);
            }

            if (is_array($valueOrValues)) {
                foreach ($valueOrValues as $value) {
                    if ($value instanceof ArgumentSet) {
                        $wouldApply = self::wouldApplyArgBuilderDirectives($value, $builder, $directiveFilter);
                        if ($wouldApply) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Apply the FieldBuilderDirectives onto the builder.
     *
     * @param  \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder  $builder
     * @param  array<string, mixed>  $args
     */
    protected static function applyFieldBuilderDirectives(object &$builder, $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): void
    {
        foreach (self::fieldBuilderDirectives($resolveInfo) as $fieldBuilderDirective) {
            $builder = $fieldBuilderDirective->handleFieldBuilder($builder, $root, $args, $context, $resolveInfo);
        }
    }

    /**
     * Would there be any FieldBuilderDirectives to apply to the builder?
     */
    protected static function wouldApplyFieldBuilderDirectives(ResolveInfo $resolveInfo): bool
    {
        return self::fieldBuilderDirectives($resolveInfo)
            ->isNotEmpty();
    }

    /**
     * @return Collection<\Nuwave\Lighthouse\Support\Contracts\FieldBuilderDirective>
     */
    protected static function fieldBuilderDirectives(ResolveInfo $resolveInfo): Collection
    {
        // @phpstan-ignore-next-line filter is not understood
        return $resolveInfo->argumentSet
            ->directives
            ->filter(Utils::instanceofMatcher(FieldBuilderDirective::class));
    }
}