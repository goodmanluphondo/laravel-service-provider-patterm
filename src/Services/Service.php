<?php

namespace GoodmanLuphondo\LaravelServiceRepositoryPattern\Services;

abstract class Service
{
    protected $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    abstract public function all();

    abstract public function find($id);
    
}