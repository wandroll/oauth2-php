<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Extension\GrantType;

use Pantarei\OAuth2\Extension\GrantTypeInterface;
use Pantarei\OAuth2\Util\ParameterUtils;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Refresh token grant type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-6
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class RefreshTokenGrantType implements GrantTypeInterface
{
  /**
   * REQUIRED. Value MUST be set to "refresh_token".
   *
   * @see http://tools.ietf.org/html/rfc6749#section-6
   */
  private $grant_type = 'refresh_token';

  /**
   * REQUIRED. The refresh token issued to the client.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-6
   */
  private $refresh_token = '';

  /**
   * OPTIONAL.  The scope of the access request as described by
   * Section 3.3.  The requested scope MUST NOT include any scope
   * not originally granted by the resource owner, and if omitted is
   * treated as equal to the scope originally granted by the
   * resource owner.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-6
   */
  private $scope = '';

  public function setRefreshToken($refresh_token)
  {
    $this->refresh_token = $refresh_token;
    return $this;
  }

  public function getRefreshToken()
  {
    return $this->refresh_token;
  }

  public function setScope($scope)
  {
    $this->scope = $scope;
    return $this;
  }

  public function getScope()
  {
    return $this->scope;
  }

  public function __construct(Request $request, Application $app)
  {
    // Validate and set refresh_token.
    if ($refresh_token = ParameterUtils::checkRefreshToken($request, $app)) {
      $this->setRefreshToken($refresh_token);
    }

    // Validate and set scope.
    if ($scope = ParameterUtils::checkScopeByRefreshToken($request, $app)) {
      $this->setScope($scope);
    }
  }

  public static function create(Request $request, Application $app)
  {
    return new static($request, $app);
  }

  public function getResponse(Request $request, Application $app)
  {
    $response = $app['oauth2.token_type.default']::create($request, $app);
    return $response->getResponse($request, $app);
  }
}
