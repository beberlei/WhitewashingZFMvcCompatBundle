<?php
/**
 * Whitewashing ZFMvcCompatBundle
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so I can send you a copy immediately.
 */

namespace Whitewashing\ZFMvcCompatBundle\Controller;

use Symfony\Component\Controller\Response;

class ZendResponse
{
    private $content = '';
    private $status = 200;
    private $headers = array();
    private $redirect = false;

    public function setHeader($name, $value, $replace = false)
    {
        if (isset($this->headers[$name]) && !$replace) {
            return $this;
        }

        $this->headers[$name] = $value;
        return $this;
    }

    public function setRedirect($url, $status = 302)
    {
        $this->redirect = $url;
        $this->status = $status;
    }

    public function generateResponse()
    {
        $response = new Response($this->content, $this->status, $this->headers);
        if ($this->redirect) {
            $response->setRedirect($this->redirect, $this->status);
        }
    }

    public function isRedirect()
    {
        return $this->redirect !== false;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function clearHeaders()
    {
        $this->headers = array();
        return $this;
    }

    public function clearHeader($name)
    {
        unset($this->headers[$name]);
        return $this;
    }

    public function clearAllHeaders()
    {
        $this->clearHeaders();
    }

    public function setHttpResponseCode($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getHttpResponseCode()
    {
        return $this->status;
    }

    public function setBody($content)
    {
        $this->content = $content;
        return $this;
    }

    public function appendBody($content)
    {
        $this->content .= $content;
        return $this;
    }

    public function clearBody()
    {
        $this->content = "";
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function __call($method, $args)
    {
        throw new \BadMethodCallException("not implemented");
    }
}
