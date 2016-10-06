<?php namespace Database\Amazon;

class AmazonSearch
{
    protected $input = array();

    protected $by = '';
    protected $reference = '';

    protected $filter_whitelist = array(
        'Accessories',
        'AlternateVersions',
        'BrowseNodes',
        'EditorialReview',
        'Images',
        'ItemAttributes',
        'ItemIds',
        'Large',
        'Medium',
        'OfferFull',
        'OfferListings',
        'Offers',
        'OfferSummary',
        'PromotionSummary',
        'RelatedItems',
        'Reviews',
        'SalesRank',
        'Similarities',
        'Small',
        'Tracks',
        'Variations',
        'VariationImages',
        'VariationMatrix',
        'VariationOffers',
        'VariationSummary'
    );

    use AmazonRequest;

    public function __construct(array $options=array())
    {
        $input = array();
        foreach ($options as $key => $value) {
            $input[strtolower($key)] = $value;
        }

        $this->process($input);
    }

    protected function parameters($timestamp)
    {
        $parameters = array();

        $parameters['AWSAccessKeyId'] = $this->publicKey();
        $parameters['AssociateTag'] = $this->userId();
        $parameters['IdType'] = $this->by;
        $parameters['ItemId'] = $this->reference;
        $parameters['Operation'] = 'ItemLookup';
        $parameters['ResponseGroup'] = $this->filter();
        $parameters['SearchIndex'] = $this->materialType();
        $parameters['Service'] = 'AWSECommerceService';
        $parameters['Timestamp'] = $timestamp;

        return $parameters;
    }

    protected function process(array $options)
    {
        $this->input = $options;

        if ($id = $this->ean()) {
            $this->by = 'EAN';
            $this->reference = $id;
        }

        if ($id = $this->sku()) {
            $this->by = 'SKU';
            $this->reference = $id;
        }

        if ($id = $this->asin()) {
            $this->by = 'ASIN';
            $this->reference = $id;
        }

        if ($id = $this->upc()) {
            $this->by = 'UPC';
            $this->reference = $id;
        }

        if ($id = $this->isbn()) {
            $this->by = 'ISBN';
            $this->reference = $id;
        }
    }

    protected function materialType()
    {
        $options = $this->input;

        $whitelist = 'Books,Movies,Music,Magazines,MP3Downloads,VideoGames';
        $whitelist = explode(',', $whitelist);

        foreach ($whitelist as $value)
        {
            $key = strtolower($value);

            if (!isset($options[$key])) { continue; }
            if (!$options[$key]) { continue; }

            return $value;
        }

        return 'All';
    }

    protected function filter()
    {
        $options = $this->input;
        $whitelist = $this->filter_whitelist;

        $filter = array();
        foreach ($whitelist as $value)
        {
            $key = strtolower($value);

            if (!isset($options[$key])) { continue; }
            if (!$options[$key]) { continue; }

            $filter[] = $value;
        }

        return (empty($filter)) ? 'Large,Images' : implode(',', $filter);
    }

    protected function getReferenceId($key, $default='')
    {
        return (isset($this->input[$key]))
            ? $this->input[$key]
            : $default;
    }

    protected function isbn()
    {
        $options = $this->input;
        $accepted_variations = array('isbn10', 'isbn13', 'isbn-10', 'isbn-13', 'isbn');

        $isbn = '';
        foreach ($accepted_variations as $variation) {
            $isbn = $this->getReferenceId($variation, $isbn);
        }

        return (string)$isbn;
    }

    protected function upc()
    {
        return $this->getReferenceId('upc');
    }

    protected function asin()
    {
        return $this->getReferenceId('asin');
    }

    protected function sku()
    {
        return $this->getReferenceId('sku');
    }

    protected function ean()
    {
        return $this->getReferenceId('ean');
    }
}
