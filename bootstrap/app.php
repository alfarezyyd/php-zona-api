<?php

  use App\Http\Middleware\EnsureEmailIsVerified;
  use App\Payloads\WebResponsePayload;
  use Illuminate\Foundation\Application;
  use Illuminate\Foundation\Configuration\Exceptions;
  use Illuminate\Foundation\Configuration\Middleware;
  use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
  use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

  return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
      web: __DIR__ . '/../routes/web.php',
      api: __DIR__ . '/../routes/api.php',
      commands: __DIR__ . '/../routes/console.php',
      health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
      $middleware->alias([
        'verified' => EnsureEmailIsVerified::class,
      ]);
      $middleware->validateCsrfTokens(
        except: ['*']
      );
      //
    })
    ->withExceptions(function (Exceptions $exceptions) {
      $exceptions->render(function (NotFoundHttpException $notFoundHttpException) {
        return response()
          ->json((new WebResponsePayload("Entity not found", errorInformation: $notFoundHttpException->getMessage()))
            ->getJsonResource())->setStatusCode(404);
      });
    })->create();
