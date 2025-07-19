<?php

namespace GoodmanLuphondo\LaravelServiceRepositoryPattern\Interfaces;

interface BaseInterface
{
    public function query();

    public function find($id);

    public function findAll();

    public function firstOrCreate(array $data);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function firstWhere(string $column, $value);

    public function where(string $column, $value);

    public function orderBy(string $column, string $direction = 'asc');
}