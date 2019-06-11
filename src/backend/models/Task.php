<?php

namespace App\models;

/**
 * @Entity @Table(name="tasks")
 */
class Task {

    /** @Id @Column(type="integer") @GeneratedValue */
    protected $id;
    /** @Column(type="string") */
    protected $name;
    /** @Column(type="string") */
    protected $image;
    /** @Column(type="string") */
    protected $cluster;
    /** @Column(type="string") */
    protected $service;
    /** @Column(type="string") */
    protected $family;
    /** @Column(type="string") */
    protected $env;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image) {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getCluster() {
        return $this->cluster;
    }

    /**
     * @param mixed $cluster
     */
    public function setCluster($cluster) {
        $this->cluster = $cluster;
    }

    /**
     * @return mixed
     */
    public function getService() {
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function setService($service) {
        $this->service = $service;
    }

    /**
     * @return mixed
     */
    public function getFamily() {
        return $this->family;
    }

    /**
     * @param mixed $family
     */
    public function setFamily($family) {
        $this->family = $family;
    }

    /**
     * @return mixed
     */
    public function getEnv() {
        return $this->env;
    }

    /**
     * @param mixed $env
     */
    public function setEnv($env) {
        $this->env = $env;
    }

}
