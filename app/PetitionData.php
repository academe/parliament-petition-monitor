<?php

namespace App;

/**
 * Represents a fetched petition data object.
 */

class PetitionData
{
    protected $data;

    protected $petitionNumber;

    /**
     *
     */
    public function __construct(int $petitionNumber, $data = null)
    {
        $this->petitionNumber = $petitionNumber;

        if (! empty($data)) {
            $this->data = (object)$data;
        }
    }

    /**
     * Format the petitino URL.
     */
    public function petitionUrl(int $petitionNumber)
    {
        return sprintf(
            'https://petition.parliament.uk/petitions/%d.json',
            $petitionNumber
        );
    }

    /**
     * Fetch a data item from the dataset using dor-notation.
     */
    public function getDataItem(string $dotPathname = '')
    {
        // If the data has not been fetched, then fetch it now.

        if ($this->data === null) {
            $url = $this->petitionUrl($this->petitionNumber);
            $this->data = json_decode(file_get_contents($url));
        }

        // Need to be explicit about returning the full dataset if
        // no dot-formatted pathname is provided.

        return $dotPathname === '' ? $this->data : data_get($this->data, $dotPathname);
    }

    public function getState() : string
    {
        return $this->getDataItem('data.attributes.state');
    }

    /** 
     * The overall declared count.
     * This often differs from the counts obtained by summing the
     * country counts or the constituency counts.
     */
    public function getCount() : int
    {
        return $this->getDataItem('data.attributes.signature_count');
    }

    /** 
     * The Main action requested of the petition.
     */
    public function getAction() : string
    {
        return $this->getDataItem('data.attributes.action');
    }

    /**
     * Is the petition still open?
     */
    public function isOpen() : bool
    {
        return $this->getState() === 'open';
    }

    /**
     * Return the main metadata - everything except the counts.
     */
    public function getMetadata()
    {
        $data = $this->getDataItem();

        if (isset($data->data->attributes->signatures_by_country)) {
            unset($data->data->attributes->signatures_by_country);
        }

        if (isset($data->data->attributes->signatures_by_constituency)) {
            unset($data->data->attributes->signatures_by_constituency);
        }

        return $data;
    }
}
