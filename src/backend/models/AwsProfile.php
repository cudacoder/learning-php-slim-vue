<?php

namespace App\models;

/**
 * @Entity @Table(name="aws_profiles")
 */
class AwsProfile {

    /** @Id @Column(type="integer") @GeneratedValue */
    protected $id;
    /** @Column(type="string") */
    protected $name;
    /** @Column(type="string") */
    protected $access;
    /** @Column(type="string") */
    protected $secret;
    /** @Column(type="string") */
    protected $region;

    /**
     * @return mixed
     */
    public function getAccess() {
        return $this->access;
    }

    /**
     * @param mixed $access
     */
    public function setAccess($access) {
        $this->access = $access;
    }

    /**
     * @return mixed
     */
    public function getSecret() {
        return $this->secret;
    }

    /**
     * @param mixed $secret
     */
    public function setSecret($secret) {
        $this->secret = $secret;
    }

    /**
     * @return mixed
     */
    public function getRegion() {
        return $this->region;
    }

    /**
     * @param mixed $region
     */
    public function setRegion($region) {
        $this->region = $region;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }
}
