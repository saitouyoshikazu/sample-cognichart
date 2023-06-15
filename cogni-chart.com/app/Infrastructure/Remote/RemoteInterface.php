<?php

namespace App\Infrastructure\Remote;

interface RemoteInterface
{

    /**
     * Send post request to remote server.
     * @param   Scheme      $scheme         The scheme is http or https.
     * @param   string      $host           The host.
     * @param   string      $url            Url of request following host.
     * @param   array       $params         Map of request parameters.
     * @param   array       $addHeaders     Map of request headers that is you want to append.
     * @param   array       $options        Array of option for example https client certs.
     * @return  RemoteResponse  When request was sent.
     *          null            When failed to send request.
     */
    public function sendPost(
        Scheme      $scheme,
        string      $host,
        string      $url = null,
        array       $params = null,
        array       $addHeaders = null,
        array       $options = null
    );

    /**
     * Send get request to remote server.
     * @param   Scheme      $scheme         The scheme is http or https.
     * @param   string      $host           The host.
     * @param   string      $url            Url of request following host.
     * @param   array       $params         Map of request parameters.
     * @param   array       $addHeaders     Map of request headers that is you want to append.
     * @param   array       $options        Array of option for example https client certs.
     * @return  RemoteResponse  When request was sent.
     *          null            When failed to send request.
     */
    public function sendGet(
        Scheme      $scheme,
        string      $host,
        string      $url = null,
        array       $params = null,
        array       $addHeaders = null,
        array       $options = null
    );

}
