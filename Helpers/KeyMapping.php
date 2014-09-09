<?php

namespace EzSystems\BehatBundle\Helpers;

trait KeyMapping
{
    /**
     * Associative array with values needed for a test that can't be passed through gherkin
     * for example, objects ID can't be defined through gherkin, so we pass something, and them map
     * it internally
     *
     * @var array Associative array
     */
    private $keyMap = array();

    /**
     * Store (map) values needed for testing that can't be passed through gherkin
     *
     * @param string $key   (Unique) Identifier key on the array
     * @param mixed $values Any kind of value/object to store
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException if $key is empty
     */
    protected function addValuesToKeyMap( $key, $values )
    {
        if ( empty( $key ) )
        {
            throw new InvalidArgumentException( 'key', "can't be empty" );
        }

        $this->keyMap[$key] = $values;
    }

    /**
     * Fetches values needed for testing stored on mapping
     *
     * @param string $key (Unique) Identifier key on the array
     *
     * @return mixed|null Mapped value or null if not found
     */
    protected function getValuesFromKeyMap( $key )
    {
        if ( empty( $key ) || empty( $this->keyMap[$key] ) )
        {
            return null;
        }

        return $this->keyMap[$key];
    }

    /**
     * Change the mapped values in key map into the intended URL
     *
     * ex:
     *  $keyMap = array(
     *      '{id}'      => 123,
     *      'another'   => 'test',
     *      '{extraId}' => 321
     *  );
     *   URL: 
     *      before: '/some/url/with/another/and/{id}'
     *      after:  '/some/url/with/test/and/123'
     *
     * @param string $url URL to update key mapped values
     *
     * @return string Updated URL
     */
    protected function changeMappedValuesOnUrl( $url )
    {
        $newUrl = "";
        foreach ( explode( '/', $url ) as $chunk )
        {
            $newChunk = $this->getValuesFromKeyMap( $chunk );
            if ( empty( $newChunk ) )
            {
                $newChunk = $chunk;
            }

            $newUrl .= '/' . $newChunk;
        }

        return preg_replace( '/\/\//', '/', $newUrl );
    }
}
