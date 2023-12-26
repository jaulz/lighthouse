<?php declare(strict_types=1);
// Generated by the protocol buffer compiler.  DO NOT EDIT!
// source: src/Tracing/FederatedTracing/reports.proto

namespace Nuwave\Lighthouse\Tracing\FederatedTracing\Proto\Trace;

use Google\Protobuf\Internal\GPBUtil;

/**
 * represents a node in the query plan, under which there is a trace tree for that service fetch.
 * In particular, each fetch node represents a call to an implementing service, and calls to implementing
 * services may not be unique. See https://github.com/apollographql/federation/blob/main/query-planner-js/src/QueryPlan.ts
 * for more information and details.
 *
 * Generated from protobuf message <code>Trace.QueryPlanNode</code>
 */
class QueryPlanNode extends \Google\Protobuf\Internal\Message
{
    protected $node;

    /**
     * Constructor.
     *
     * @param  array  $data {
     *     Optional. Data for populating the Message object.
     *
     *     @var QueryPlanNode\SequenceNode $sequence
     *     @var QueryPlanNode\ParallelNode $parallel
     *     @var QueryPlanNode\FetchNode $fetch
     *     @var QueryPlanNode\FlattenNode $flatten
     *     @var QueryPlanNode\DeferNode $defer
     *     @var QueryPlanNode\ConditionNode $condition
     * }
     */
    public function __construct($data = null)
    {
        \Nuwave\Lighthouse\Tracing\FederatedTracing\Proto\Metadata\Reports::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.Trace.QueryPlanNode.SequenceNode sequence = 1 [json_name = "sequence"];</code>.
     *
     * @return QueryPlanNode\SequenceNode|null
     */
    public function getSequence()
    {
        return $this->readOneof(1);
    }

    public function hasSequence()
    {
        return $this->hasOneof(1);
    }

    /**
     * Generated from protobuf field <code>.Trace.QueryPlanNode.SequenceNode sequence = 1 [json_name = "sequence"];</code>.
     *
     * @param  QueryPlanNode\SequenceNode  $var
     *
     * @return $this
     */
    public function setSequence($var)
    {
        GPBUtil::checkMessage($var, QueryPlanNode\SequenceNode::class);
        $this->writeOneof(1, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Trace.QueryPlanNode.ParallelNode parallel = 2 [json_name = "parallel"];</code>.
     *
     * @return QueryPlanNode\ParallelNode|null
     */
    public function getParallel()
    {
        return $this->readOneof(2);
    }

    public function hasParallel()
    {
        return $this->hasOneof(2);
    }

    /**
     * Generated from protobuf field <code>.Trace.QueryPlanNode.ParallelNode parallel = 2 [json_name = "parallel"];</code>.
     *
     * @param  QueryPlanNode\ParallelNode  $var
     *
     * @return $this
     */
    public function setParallel($var)
    {
        GPBUtil::checkMessage($var, QueryPlanNode\ParallelNode::class);
        $this->writeOneof(2, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Trace.QueryPlanNode.FetchNode fetch = 3 [json_name = "fetch"];</code>.
     *
     * @return QueryPlanNode\FetchNode|null
     */
    public function getFetch()
    {
        return $this->readOneof(3);
    }

    public function hasFetch()
    {
        return $this->hasOneof(3);
    }

    /**
     * Generated from protobuf field <code>.Trace.QueryPlanNode.FetchNode fetch = 3 [json_name = "fetch"];</code>.
     *
     * @param  QueryPlanNode\FetchNode  $var
     *
     * @return $this
     */
    public function setFetch($var)
    {
        GPBUtil::checkMessage($var, QueryPlanNode\FetchNode::class);
        $this->writeOneof(3, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Trace.QueryPlanNode.FlattenNode flatten = 4 [json_name = "flatten"];</code>.
     *
     * @return QueryPlanNode\FlattenNode|null
     */
    public function getFlatten()
    {
        return $this->readOneof(4);
    }

    public function hasFlatten()
    {
        return $this->hasOneof(4);
    }

    /**
     * Generated from protobuf field <code>.Trace.QueryPlanNode.FlattenNode flatten = 4 [json_name = "flatten"];</code>.
     *
     * @param  QueryPlanNode\FlattenNode  $var
     *
     * @return $this
     */
    public function setFlatten($var)
    {
        GPBUtil::checkMessage($var, QueryPlanNode\FlattenNode::class);
        $this->writeOneof(4, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Trace.QueryPlanNode.DeferNode defer = 5 [json_name = "defer"];</code>.
     *
     * @return QueryPlanNode\DeferNode|null
     */
    public function getDefer()
    {
        return $this->readOneof(5);
    }

    public function hasDefer()
    {
        return $this->hasOneof(5);
    }

    /**
     * Generated from protobuf field <code>.Trace.QueryPlanNode.DeferNode defer = 5 [json_name = "defer"];</code>.
     *
     * @param  QueryPlanNode\DeferNode  $var
     *
     * @return $this
     */
    public function setDefer($var)
    {
        GPBUtil::checkMessage($var, QueryPlanNode\DeferNode::class);
        $this->writeOneof(5, $var);

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Trace.QueryPlanNode.ConditionNode condition = 6 [json_name = "condition"];</code>.
     *
     * @return QueryPlanNode\ConditionNode|null
     */
    public function getCondition()
    {
        return $this->readOneof(6);
    }

    public function hasCondition()
    {
        return $this->hasOneof(6);
    }

    /**
     * Generated from protobuf field <code>.Trace.QueryPlanNode.ConditionNode condition = 6 [json_name = "condition"];</code>.
     *
     * @param  QueryPlanNode\ConditionNode  $var
     *
     * @return $this
     */
    public function setCondition($var)
    {
        GPBUtil::checkMessage($var, QueryPlanNode\ConditionNode::class);
        $this->writeOneof(6, $var);

        return $this;
    }

    /** @return string */
    public function getNode()
    {
        return $this->whichOneof('node');
    }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryPlanNode::class, \Nuwave\Lighthouse\Tracing\FederatedTracing\Proto\Trace_QueryPlanNode::class);