<?php

/*
 * This file is part of the Liip/ThemeBundle
 *
 * (c) Liip AG
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Liip\ThemeBundle\Controller;

use Liip\ThemeBundle\ActiveTheme;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Theme controller
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class ThemeController
{
    protected $activeTheme;

    /**
     * Available themes
     * 
     * @var array
     */
    protected $themes;

    /**
     * Name of the cookie to store active theme
     * 
     * @var string
     */
    protected $cookieName;

    /**
     * Theme controller construct
     * 
     * @param ActiveTheme $activeTheme active theme instance
     * @param array       $themes      Available themes
     * @param string      $cookieName  cookie name to store active theme
     */
    public function __construct(ActiveTheme $activeTheme, array $themes, $cookieName)
    {
        $this->activeTheme = $activeTheme;
        $this->themes      = $themes;
        $this->cookieName  = $cookieName;
    }

    /**
     * Switch theme
     *
     * @param Request $request actual request
     *
     * @return RedirectResponse
     *
     * @throws NotFoundHttpException when theme name not exists
     */
    public function switchAction(Request $request)
    {
        $theme = $request->get('theme');

        if (!in_array($theme, $this->themes)) {
            throw new NotFoundHttpException(sprintf('The theme "%s" does not exist', $theme));
        }

        $this->activeTheme->setName($theme);

        $url = $request->headers->get('Referer');
        $cookie = new Cookie($this->cookieName, $theme, time()+60*60*24*365, '/', null, false, false);

        $response = new RedirectResponse($url);
        $response->headers->setCookie($cookie);

        return $response;
    }
}