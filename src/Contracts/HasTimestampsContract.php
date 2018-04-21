<?php namespace Yorki\Repositories\Contracts;

interface HasTimestampsContract {
    /**
     * Update the model's update timestamp.
     *
     * @return bool
     */
    public function touch();

    /**
     * Set the value of the "created at" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * Set the value of the "updated at" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setUpdatedAt($value);

    /**
     * Get a fresh timestamp for the model.
     *
     * @return \Illuminate\Support\Carbon
     */
    public function freshTimestamp();

    /**
     * Get a fresh timestamp for the model.
     *
     * @return string
     */
    public function freshTimestampString();

    /**
     * Determine if the model uses timestamps.
     *
     * @return bool
     */
    public function usesTimestamps();

    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
    public function getCreatedAtColumn();

    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
    public function getUpdatedAtColumn();
}