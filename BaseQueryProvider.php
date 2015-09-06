<?php

namespace iriscrm\SimplePOData;

use POData\Providers\Metadata\ResourceProperty;
use POData\Providers\Metadata\ResourceSet;
use POData\UriProcessor\QueryProcessor\Expression\Parser\IExpressionProvider;
use POData\UriProcessor\ResourcePathProcessor\SegmentParser\KeyDescriptor;
use POData\Providers\Query\IQueryProvider;
use POData\Providers\Expression\MySQLExpressionProvider;
use POData\Providers\Query\QueryType;
use POData\Providers\Query\QueryResult;

abstract class BaseQueryProvider implements IQueryProvider
{
    /**
    * @var Connection
    */
    protected $db;

    public function __construct($db){
        $this->db = $db;
    }

    abstract protected function queryAll($sql, $parameters = null);

    abstract protected function queryScalar($sql, $parameters = null);

    /* Stubbed Implementaiton Here */
    public function getQueryProvider()
    {
        return new QueryProvider();
    }

    public function handlesOrderedPaging()
    {
        return true;
    }

    public function getExpressionProvider()
    {
        return new MySQLExpressionProvider();
    }

    protected function getEntityName($entityClassName)
    {
        preg_match_all('/\\\([a-zA-Z]+)/', $entityClassName, $matches);
        if (!empty($matches[1])) {
            return $matches[1][count($matches[1]) - 1];
        }
        return $entityClassName;
    }

    protected function getTableName($entityName)
    {
        $tableName = $entityName;
        preg_match_all('/[A-Z][a-z]+/', $entityName, $matches);
        if (!empty($matches[0])) {
            $tableName = implode('_', $matches[0]);
        }
        return strtolower($tableName);
    }

    public function getResourceSet(
        QueryType $queryType,
        ResourceSet $resourceSet,
        $filterInfo = null,
        $orderBy = null,
        $top = null,
        $skip = null
    ) {
        $result = new QueryResult();

        $entityClassName = $resourceSet->getResourceType()->getInstanceType()->name;
        $entityName = $this->getEntityName($entityClassName);
        $tableName = $this->getTableName($entityName);

        $option = null;
        if ($queryType == QueryType::ENTITIES_WITH_COUNT()){
            //tell mysql we want to know the count prior to the LIMIT 
            //$option = 'SQL_CALC_FOUND_ROWS';
        }

        $where = $filterInfo ? ' WHERE ' . $filterInfo->getExpressionAsString(): '';
        $sqlCount = 'SELECT COUNT(*) FROM ' . $tableName . $where;
        if ($queryType == QueryType::ENTITIES() || $queryType == QueryType::ENTITIES_WITH_COUNT()) {
            $sql = 'SELECT ' . $option . ' * FROM ' . $tableName . $where 
                    . ' LIMIT ' . $top . ' OFFSET ' . $skip;
            $data = $this->queryAll($sql);
            
            if ($queryType == QueryType::ENTITIES_WITH_COUNT()){
                //get those found rows
                //$result->count = $this->queryScalar('SELECT FOUND_ROWS()');
                $result->count = $this->queryScalar($sqlCount);
            }

            $result->results = array_map($entityClassName . '::fromRecord', $data);
        }
        elseif ($queryType == QueryType::COUNT()) {
            $result->count = QueryResult::adjustCountForPaging(
                $this->queryScalar($sqlCount), $top, $skip);
        }

        return $result;
    }

    public function getResourceFromResourceSet(
        ResourceSet $resourceSet,
        KeyDescriptor $keyDescriptor
    ) {
        $where = '';
        $parameters = [];
        $index = 0;
        foreach ($keyDescriptor->getValidatedNamedValues() as $key => $value) {
            $index++;
            //Keys have already been validated, so this is not a SQL injection surface 
            $where .= $where ? ' AND ' : '';
            $where .= $key . ' = :param' . $index;
            $parameters[':param' . $index] = $value[0];
        }
        $where = $where ? ' WHERE ' . $where : '';

        $entityClassName = $resourceSet->getResourceType()->getInstanceType()->name;
        $entityName = $this->getEntityName($entityClassName);

        $sql = 'SELECT * FROM ' . $this->getTableName($entityName) . $where . ' LIMIT 1';
        $result = $this->queryAll($sql, $parameters);
        if ($result) {
            $result = $result[0];
        }

        return $entityClassName::fromRecord($result);
    }

    public function getRelatedResourceSet(
        QueryType $queryType,
        ResourceSet $sourceResourceSet,
        stdClass $sourceEntityInstance,
        ResourceSet $targetResourceSet,
        ResourceProperty $targetProperty,
        $filter = null,
        $orderBy = null,
        $top = null,
        $skip = null
    ) {
        return null;
    }

    public function getResourceFromRelatedResourceSet(
        ResourceSet $sourceResourceSet,
        stdClass $sourceEntityInstance,
        ResourceSet $targetResourceSet,
        ResourceProperty $targetProperty,
        KeyDescriptor $keyDescriptor
    ) {
        return null;
    }

    public function getRelatedResourceReference(
        ResourceSet $sourceResourceSet,
        stdClass $sourceEntityInstance,
        ResourceSet $targetResourceSet,
        ResourceProperty $targetProperty
    ) {
        return null;
    }

}