<?php namespace JobApis\Jobs\Client;

class MultiCollection extends Collection
{
    /**
     * Append a collection to this collection.
     *
     * @param Collection $collection
     *
     * @return $this
     */
    public function addItems(Collection $collection)
    {
        // If there are jobs, add them to the collection
        if ($collection->count()) {
            foreach ($collection->all() as $job) {
                $this->add($job);
            }
        }
        // If there are errors, add them to the collection
        if ($collection->getErrors()) {
            foreach ($collection->getErrors() as $error) {
                $this->addError($error);
            }
        }

        return $this;
    }

    /**
     * Filter items by a field having a specific value.
     *
     * @param string $field
     * @param string $value
     * @param string $operator
     *
     * @return $this
     */
    public function filter($field, $value, $operator = '=')
    {
        $this->items = array_filter(
            $this->items,
            function ($item) use ($field, $value, $operator) {
                if ($operator == '>') {
                    return $item->{$field} > $value;
                } elseif ($operator == '<') {
                    return $item->{$field} < $value;
                } elseif ($operator == '=') {
                    return $item->{$field} == $value;
                }
                return false;
            }
        );

        return $this;
    }

    /**
     * Order items by a field value.
     *
     * @param string $orderBy
     * @param string $order
     *
     * @return $this
     */
    public function orderBy($orderBy, $order = 'desc')
    {
        // Order by date
        usort(
            $this->items,
            function ($item1, $item2) use ($orderBy, $order) {
                if ($item1->{$orderBy} == $item2->{$orderBy}) {
                    return 0;
                }

                if ($order === 'asc') {
                    return $item1->{$orderBy} < $item2->{$orderBy} ? -1 : 1;
                }
                return $item1->{$orderBy} > $item2->{$orderBy} ? -1 : 1;
            }
        );

        return $this;
    }

    /**
     * Truncate the items to a maximum number of results.
     *
     * @param null $max
     *
     * @return $this
     */
    public function truncate($max = null)
    {
        if ($max) {
            $this->items = array_slice($this->items, 0, $max);
        }

        return $this;
    }
}