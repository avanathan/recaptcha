<?php
/**
 * This is a PHP library that handles calling reCAPTCHA.
 *
 * @copyright Copyright (c) 2015, Google Inc.
 * @link      https://www.google.com/recaptcha
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace ReCaptcha\RequestMethod;

use \ReCaptcha\ReCaptcha;
use \ReCaptcha\RequestParameters;
use PHPUnit\Framework\TestCase;

class CurlPostTest extends TestCase
{
    protected function setUp()
    {
        if (!extension_loaded('curl')) {
            $this->markTestSkipped(
                    'The cURL extension is not available.'
            );
        }
    }

    public function testSubmit()
    {
        $curl = $this->getMockBuilder(\ReCaptcha\RequestMethod\Curl::class)
            ->disableOriginalConstructor()
            ->setMethods(array('init', 'setoptArray', 'exec', 'close'))
            ->getMock();
        $curl->expects($this->once())
                ->method('init')
                ->willReturn(new \stdClass);
        $curl->expects($this->once())
                ->method('setoptArray')
                ->willReturn(true);
        $curl->expects($this->once())
                ->method('exec')
                ->willReturn('RESPONSEBODY');
        $curl->expects($this->once())
                ->method('close');

        $pc = new CurlPost($curl);
        $response = $pc->submit(new RequestParameters("secret", "response"));
        $this->assertEquals('RESPONSEBODY', $response);
    }

    public function testOverrideSiteVerifyUrl()
    {
        $url = 'OVERRIDE';

        $curl = $this->getMockBuilder(\ReCaptcha\RequestMethod\Curl::class)
            ->disableOriginalConstructor()
            ->setMethods(array('init', 'setoptArray', 'exec', 'close'))
            ->getMock();
        $curl->expects($this->once())
                ->method('init')
                ->with($url)
                ->willReturn(new \stdClass);
        $curl->expects($this->once())
                ->method('setoptArray')
                ->willReturn(true);
        $curl->expects($this->once())
                ->method('exec')
                ->willReturn('RESPONSEBODY');
        $curl->expects($this->once())
                ->method('close');

        $pc = new CurlPost($curl, $url);
        $response = $pc->submit(new RequestParameters("secret", "response"));
        $this->assertEquals('RESPONSEBODY', $response);
    }

    public function testConnectionFailureReturnsError()
    {
        $curl = $this->getMockBuilder(\ReCaptcha\RequestMethod\Curl::class)
            ->disableOriginalConstructor()
            ->setMethods(array('init', 'setoptArray', 'exec', 'close'))
            ->getMock();
        $curl->expects($this->once())
                ->method('init')
                ->willReturn(new \stdClass);
        $curl->expects($this->once())
                ->method('setoptArray')
                ->willReturn(true);
        $curl->expects($this->once())
                ->method('exec')
                ->willReturn(false);
        $curl->expects($this->once())
                ->method('close');

        $pc = new CurlPost($curl);
        $response = $pc->submit(new RequestParameters("secret", "response"));
        $this->assertEquals('{"success": false, "error-codes": ["'.ReCaptcha::E_CONNECTION_FAILED.'"]}', $response);
    }
}
