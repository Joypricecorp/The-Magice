<?php

/*
 * This file is part of the The Magice Project.
 *
 * @copyright   2012-2014 ツ Joyprice corporation Ltd.
 * @license     http://www.joyprice.org/license
 * @link        http://www.joyprice.org/themagice
 */

namespace Magice\Security\EntryPoint {

    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Security\Core\Exception\AuthenticationException;
    use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
    use Symfony\Component\Security\Http\HttpUtils;
    use Symfony\Component\HttpKernel\HttpKernelInterface;

    /**
     * FormAuthenticationEntryPoint starts an authentication via a login form.
     */
    class FormAuthenticationEntryPoint implements AuthenticationEntryPointInterface
    {
        private $loginPath;
        private $useForward;
        private $httpKernel;
        private $httpUtils;

        /**
         * Constructor.
         *
         * @param HttpKernelInterface $kernel
         * @param HttpUtils           $httpUtils  An HttpUtils instance
         * @param string              $loginPath  The path to the login form
         * @param Boolean             $useForward Whether to forward or redirect to the login form
         */
        public function __construct(HttpKernelInterface $kernel, HttpUtils $httpUtils, $loginPath, $useForward = false)
        {
            $this->httpKernel = $kernel;
            $this->httpUtils  = $httpUtils;
            $this->loginPath  = $loginPath;
            $this->useForward = (Boolean) $useForward;
        }

        /**
         * {@inheritdoc}
         */
        public function start(Request $request, AuthenticationException $authException = null)
        {
            // if ajax
            // TODO: improve me by custom with service config
            if ($request->isXmlHttpRequest()) {
                $response = new JsonResponse(array(
                    'error'   => true,
                    'success' => false,
                    'msg'     => $authException->getMessage(),
                    'code'    => $authException->getCode()
                ), 401);

                $response->headers->set('X-Status-Code', 401);
                return $response;
            }

            if ($this->useForward) {
                $subRequest = $this->httpUtils->createRequest($request, $this->loginPath);

                $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
                if (200 === $response->getStatusCode()) {
                    $response->headers->set('X-Status-Code', 401);
                }

                return $response;
            }

            return $this->httpUtils->createRedirectResponse($request, $this->loginPath);
        }
    }
}