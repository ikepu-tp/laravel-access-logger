<?php

namespace ikepu_tp\AccessLogger\app\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use ikepu_tp\AccessLogger\app\Models\Log;
use Symfony\Component\HttpFoundation\Response;
use Jenssegers\Agent\Agent;
use ikepu_tp\AccessLogger\app\Models\Log_head;
use ikepu_tp\AccessLogger\app\Models\Log_info;
use ikepu_tp\AccessLogger\app\Models\Log_request;
use ikepu_tp\AccessLogger\app\Models\Log_response;
use ikepu_tp\AccessLogger\app\Models\Log_server;

class AccessLoggerMiddleware
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var Log
     */
    protected $log;

    /**
     * @var Log_request
     */
    protected $log_request;

    /**
     * @var \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\Response $response
     */
    protected $response;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $key = null): Response
    {
        $this->key = $key;
        $this->request = $request;
        $driver = config("access-logger.driver", "file");

        if ($driver === "database") $this->handleDatabaseBefore();

        $response = $next($request);
        $this->response = $response;

        if ($driver === "file") $this->handleFile();
        if ($driver === "database") $this->handleDatabaseAfter();

        return $response;
    }

    public function handleFile(): void
    {
        $log = [
            "log" => $this->createLog(),
            "info" => $this->createLogInfo(),
            "request" => $this->createLogRequest(),
            "head" => $this->createLogHead(),
            "server" => $this->createLogServer(),
            "response" => $this->createLogResponse(),
        ];
        FacadesLog::channel($this->key)->debug("access_log", $log);
    }

    public function handleDatabaseBefore(): void
    {
        $this->log = $this->saveDatabase(Log::class, $this->createLog());
        $this->saveDatabase(Log_info::class, $this->addLogId($this->createLogInfo()));
        $this->log_request = $this->saveDatabase(Log_request::class, $this->addLogId($this->createLogRequest()));
        if (!Log_head::insert($this->createLogHead(true))) throw new Exception("failed to save Log_head.");
        if (!Log_server::insert($this->createLogServer(true))) throw new Exception("failed to save Log_server.");
    }

    public function handleDatabaseAfter(): void
    {
        $user = $this->getUser();
        if ($this->log->user_id !== $user) $this->log->fill(["user_id" => $user])->save();
        $routeName = Route::currentRouteName();
        if ($this->log_request->route_name !== $routeName) $this->log_request->fill(["route_name" => $routeName])->save();
        $this->saveDatabase(Log_response::class, $this->addLogId($this->createLogResponse()));
    }

    public function saveDatabase(string $table, $attributes): \Illuminate\Database\Eloquent\Model
    {
        $model = new $table();
        $model->fill($attributes);
        if (!$model->save()) throw new Exception("failed to save {$table}.");
        return $model;
    }

    public function getUser()
    {
        $gurad = config("access-logger.guard", false);
        if ($gurad) $gurad = config("access-logger.guards.{$this->key}", null);
        return $this->request->user($gurad)?->getKey();
    }

    public function createLog(): array
    {
        return [
            "user_id" => $this->getUser(),
            "key" => $this->key,
        ];
    }

    public function addLogId(array $array): array
    {
        if (!$this->log) return $array;
        $array["log_id"] = $this->log->id;
        return $array;
    }

    public function createLogInfo(): array
    {
        $agent = new Agent();
        $device = $agent->deviceType();
        $browser = $agent->browser();
        return [
            "ip_address" => $this->request->ip(),
            "user_agent" => $this->request->userAgent(),
            "device" => $device,
            "browser" => $browser,
        ];
    }

    public function createLogRequest(): array
    {
        return [
            "path" => $this->request->path(),
            "route_name" => Route::currentRouteName(),
            "method" => $this->request->method(),
            "queries" => empty($this->request->query()) ? null : $this->request->query(),
            "bodies" => empty($this->request->input()) ? null : $this->request->except(config("access-logger.except")),
        ];
    }

    public function createLogHead(bool $withLogId = false): array
    {
        $log_each = [];
        if ($withLogId) {
            if (!$this->log) return [];
            $log_each = [
                "log_id" => $this->log->id,
                "created_at" => now(),
                "updated_at" => now(),
            ];
        }
        $log = [];
        foreach ($this->request->header() as $key => $value) {
            $log_each["head_key"] = $key;
            $log_each["head_value"] = $value[0];
            $log[] = $log_each;
        }
        return $log;
    }

    public function createLogServer(bool $withLogId = false): array
    {
        $log_each = [];
        if ($withLogId) {
            if (!$this->log) return [];
            $log_each = [
                "log_id" => $this->log->id,
                "created_at" => now(),
                "updated_at" => now(),
            ];
        }
        $log = [];
        foreach ($this->request->header() as $key => $value) {
            $log_each["server_key"] = $key;
            $log_each["server_value"] = $value[0];
            $log[] = $log_each;
        }
        return $log;
    }

    public function createLogResponse(): array
    {
        $resource = null;
        $original = $this->response->original;
        if ($original instanceof View)
            $resource = [
                //"data" => $original->getData(),
                "view" => $original->getName(),
                "path" => $original->getPath(),
            ];
        if (is_array($original)) $resource = $original;
        return [
            "status_code" => $this->response->getStatusCode(),
            "resources" => $resource,
        ];
    }
}
