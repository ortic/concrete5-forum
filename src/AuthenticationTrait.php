<?php

namespace Concrete\Package\OrticForum\Src;

use Concrete\Core\User\PostLoginLocation;
use Page;
use Concrete\Core\Routing\Redirect;

trait AuthenticationTrait
{
    /**
     * Forwards the user to the login page, but registers the current forum page as the post login url to ensure that
     * the user lands on the forum after logging in.
     *
     * @return \Concrete\Core\Routing\RedirectResponse
     */
    public function login()
    {
        $currentPage = Page::getCurrentPage();

        $pageLoginLocation = $this->app->make(PostLoginLocation::class);
        $pageLoginLocation->setSessionPostLoginUrl($currentPage->getCollectionLink());

        return Redirect::to('/login');
    }

    /**
     * Forwards the user to the registration page.
     *
     * @return \Concrete\Core\Routing\RedirectResponse
     */
    public function register()
    {
        return Redirect::to('/register');
    }
}