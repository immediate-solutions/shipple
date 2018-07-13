<?php
namespace ImmediateSolutions\Shipple\Tests\Mock\Comparator;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class Request implements ServerRequestInterface
{
    private $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getProtocolVersion()
    {
        // TODO: Implement getProtocolVersion() method.
    }

    public function withProtocolVersion($version)
    {
        // TODO: Implement withProtocolVersion() method.
    }

    public function getHeaders()
    {
        // TODO: Implement getHeaders() method.
    }

    public function hasHeader($name)
    {
        // TODO: Implement hasHeader() method.
    }

    public function getHeader($name)
    {
        // TODO: Implement getHeader() method.
    }

    public function getHeaderLine($name)
    {
        // TODO: Implement getHeaderLine() method.
    }

    public function withHeader($name, $value)
    {
        // TODO: Implement withHeader() method.
    }

    public function withAddedHeader($name, $value)
    {
        // TODO: Implement withAddedHeader() method.
    }

    public function withoutHeader($name)
    {
        // TODO: Implement withoutHeader() method.
    }

    public function withBody(StreamInterface $body)
    {
        // TODO: Implement withBody() method.
    }

    public function getBody()
    {
        return new class($this->data['body'] ?? []) implements StreamInterface {

            private $data = '';

            public function __construct(string $data)
            {
                $this->data = $data;
            }

            public function __toString()
            {
                return $this->data;
            }

            public function close()
            {
                // TODO: Implement close() method.
            }

            public function detach()
            {
                // TODO: Implement detach() method.
            }

            /**
             * Get the size of the stream if known.
             *
             * @return int|null Returns the size in bytes if known, or null if unknown.
             */
            public function getSize()
            {
                // TODO: Implement getSize() method.
            }

            /**
             * Returns the current position of the file read/write pointer
             *
             * @return int Position of the file pointer
             * @throws \RuntimeException on error.
             */
            public function tell()
            {
                // TODO: Implement tell() method.
            }

            /**
             * Returns true if the stream is at the end of the stream.
             *
             * @return bool
             */
            public function eof()
            {
                // TODO: Implement eof() method.
            }

            /**
             * Returns whether or not the stream is seekable.
             *
             * @return bool
             */
            public function isSeekable()
            {
                // TODO: Implement isSeekable() method.
            }

            public function seek($offset, $whence = SEEK_SET)
            {
                // TODO: Implement seek() method.
            }

            public function rewind()
            {
                // TODO: Implement rewind() method.
            }

            public function isWritable()
            {
                // TODO: Implement isWritable() method.
            }

            public function write($string)
            {
                // TODO: Implement write() method.
            }

            public function isReadable()
            {
                // TODO: Implement isReadable() method.
            }

            public function read($length)
            {
                // TODO: Implement read() method.
            }

            public function getContents()
            {
                return $this->data;
            }

            public function getMetadata($key = null)
            {
                // TODO: Implement getMetadata() method.
            }
        };
    }

    public function getRequestTarget()
    {
        // TODO: Implement getRequestTarget() method.
    }

    public function withRequestTarget($requestTarget)
    {
        // TODO: Implement withRequestTarget() method.
    }

    public function getMethod()
    {
        // TODO: Implement getMethod() method.
    }

    public function withMethod($method)
    {
        // TODO: Implement withMethod() method.
    }

    public function getUri()
    {
        return new class($this->data['uri'] ?? []) implements UriInterface
        {
            private $data;

            public function __construct(array $data)
            {
                $this->data;
            }

            public function getScheme()
            {
                // TODO: Implement getScheme() method.
            }

            public function getAuthority()
            {
                // TODO: Implement getAuthority() method.
            }

            public function getUserInfo()
            {
                // TODO: Implement getUserInfo() method.
            }

            public function getHost()
            {
                // TODO: Implement getHost() method.
            }

            public function getPort()
            {
                // TODO: Implement getPort() method.
            }

            public function getPath()
            {
                return $this->data['path'] ?? [];
            }

            public function getQuery()
            {
                return $this->data['query'] ?? [];
            }

            public function getFragment()
            {
                // TODO: Implement getFragment() method.
            }

            public function withScheme($scheme)
            {
                // TODO: Implement withScheme() method.
            }

            public function withUserInfo($user, $password = null)
            {
                // TODO: Implement withUserInfo() method.
            }

            public function withHost($host)
            {
                // TODO: Implement withHost() method.
            }

            public function withPort($port)
            {
                // TODO: Implement withPort() method.
            }

            public function withPath($path)
            {
                // TODO: Implement withPath() method.
            }

            public function withQuery($query)
            {
                // TODO: Implement withQuery() method.
            }

            public function withFragment($fragment)
            {
                // TODO: Implement withFragment() method.
            }

            public function __toString()
            {
                // TODO: Implement __toString() method.
            }
        };
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        // TODO: Implement withUri() method.
    }

    public function getServerParams()
    {
        // TODO: Implement getServerParams() method.
    }

    public function getCookieParams()
    {
        // TODO: Implement getCookieParams() method.
    }

    public function withCookieParams(array $cookies)
    {
        // TODO: Implement withCookieParams() method.
    }

    public function getQueryParams()
    {
        return $this->data['query'] ?? [];
    }

    public function withQueryParams(array $query)
    {
        // TODO: Implement withQueryParams() method.
    }

    public function getUploadedFiles()
    {
        return $this->data['files'] ?? [];
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        // TODO: Implement withUploadedFiles() method.
    }

    public function getParsedBody()
    {
        return $this->data['data'] ?? [];
    }

    public function withParsedBody($data)
    {
        // TODO: Implement withParsedBody() method.
    }

    public function getAttributes()
    {
        // TODO: Implement getAttributes() method.
    }

    public function getAttribute($name, $default = null)
    {
        // TODO: Implement getAttribute() method.
    }

    public function withAttribute($name, $value)
    {
        // TODO: Implement withAttribute() method.
    }

    public function withoutAttribute($name)
    {
        // TODO: Implement withoutAttribute() method.
    }
}