<?php

namespace Ngomory;

class Helpy
{

    /**
     * dateFormat
     *
     * @param string|null $datetime String datetime value or null. can have the value 'now'.
     * @param string $fromFormat The format of the input datetime string.
     * @param string $toFormat The format of the output datetime string.
     * @return string The formatted datetime string.
     */
    public static function dateFormat($datetime, $toFormat = 'd/m/Y H:i:s', $fromFormat = 'Y-m-d H:i:s'): string
    {
        $datetime = ($datetime == 'now') ? date('Y-m-d H:i:s') : $datetime;
        $date = \DateTime::createFromFormat($fromFormat, $datetime);
        return $date ? $date->format($toFormat) : '';
    }

    /**
     * dateToLocal
     *
     * @param string|null $datetime String datetime value or null. can have the value 'now'.
     * @param string $local String local code fr|en.
     * @param boolean $time Display or not the date.
     * @return string
     */
    public static function dateToLocal($datetime, string $local = 'fr', bool $showTime = true): string
    {
        switch ($local) {
            case 'fr':
                $toFormat = 'd/m/Y' . ($showTime ? ' \à H:i:s' : '');
                return self::dateFormat($datetime, $toFormat);
                break;
            case 'en':
                $toFormat = 'Y-m-d' . ($showTime ? ' \a\t H:i:s' : '');
                return self::dateFormat($datetime, $toFormat);
                break;
            default:
                return '';
                break;
        }
    }

    /**
     * numberFormat
     *
     * @param float $number
     * @param integer $decimals Number of digits decimal
     * @param string $decimalSeparator
     * @param string $thousandSeparator
     * @return string
     */
    public static function numberFormat(float $number, int $decimals = 2, string $decimalSeparator = ',', string $thousandSeparator = ' '): string
    {
        return number_format($number, $decimals, $decimalSeparator, $thousandSeparator);
    }

    /**
     * numberToLocal
     *
     * @param float $number
     * @param string $local String local code fr|en
     * @param integer $decimals Number of digits decimal
     * @return string
     */
    public static function numberToLocal(float $number, string $local = 'fr', int $decimals = 2): string
    {
        switch ($local) {
            case 'fr':
                return self::numberFormat($number, $decimals);
                break;
            case 'en':
                return self::numberFormat($number, $decimals, '.', ',');
                break;
            default:
                return $number;
                break;
        }
    }

    /**
     * strRandom
     * 
     * Generates a random string of a specified length with optional include and exclude characters.
     *
     * @param int $length The length of the result string. Default is 16.
     * @param array $options An associative array with 'include' and 'excluded' keys.
     * @param string|null $options['characters'] Optional characters use in the random string. Default is an empty string.
     * @param string|null $options['include'] Optional characters to include in the random string. Default is an empty string.
     * @param string|null $options['excluded'] Optional characters to exclude from the random string. Default is an empty string.
     *
     * @return string A random string of the specified length, using the provided include and exclude characters.
     */
    public static function strRandom(int $length = 16, array $options = ['characters' => '', 'include' => '', 'excluded' => '']): string
    {

        $characters = $options['characters'] ?? '';
        if (empty($characters)) {
            $characters = '0123456789';
            $characters .= 'abcdefghijklmnopqrstuvwxyz';
            $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $characters = str_split($characters);

        $characters = array_merge($characters, str_split($options['include'] ?? ''));
        $characters = array_diff($characters, str_split($options['excluded'] ?? ''));
        shuffle($characters);

        $charactersLength = count($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * defaultSchemaColumn
     * 
     * This method sets default schema columns based on the provided group and state.
     *
     * @param \Illuminate\Database\Schema\Blueprint $table
     * @param string $group Defaults to 'default'.
     * @param int $state Defaults to 1.
     * @return void
     */
    static function defaultSchemaColumn($table, string $group = 'defaut', int $state = 1): void
    {
        switch ($group) {
            case 'state':
                // For state
                $table->boolean('state')->default($state)->nullable();
                $table->bigInteger('state_by')->nullable();
                $table->dateTime('state_at')->nullable();
                break;
            case 'timestamp':
                // For soft delete
                $table->bigInteger('created_by')->nullable();
                $table->dateTime('created_at')->nullable();
                $table->bigInteger('updated_by')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->bigInteger('deleted_by')->nullable();
                $table->dateTime('deleted_at')->nullable();
                break;
            default:
                // Default table columns
                $table->collation = 'utf8mb4_general_ci';
                $table->increments('id')->unsigned();
                $table->uuid('uuid')->unique()->nullable();
                break;
        }
    }

    /**
     * strToEmoji
     * 
     * Converts a string to emoji.
     *
     * @param string $string The input string to be converted to emoji.
     * @return string The input string with emoji replacements.
     */
    static function strToEmoji(string $string): string
    {
        return implode(
            '',
            array_map(
                fn ($letter) => mb_chr(ord($letter) % 32 + 0x1F1E5),
                str_split($string)
            )
        );
    }

    /**
     * moduleFilter
     *
     * @param \Illuminate\Database\Eloquent\Model $model The Eloquent model instance representing the data to be filtered.
     * @param string $filter The filter string containing filtering criteria.
     * @param array $options An associative array of additional options for customizing the filtering behavior.
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    static function moduleFilter($model, string $filter, array $options = [])
    {

        $columns = $options['columns'] ?? ['id'];
        $relations = $options['relations'] ?? [];
        $querying = $options['querying'] ?? ['random', 'limit', 'order_by'];

        /**
         * Recovery of the filter list
         */
        $filters = explode('|', $filter);

        foreach ($filters as $part) {

            /**
             * Recovery of sections of a filter part
             */
            $sections = explode(':', trim($part));

            if (count($sections) == 1) {

                $keword = trim($sections[0]);

                $model = $model->where(function ($query) use ($keword, $columns) {
                    foreach ($columns as $key => $column) {
                        $action = $key == 0 ? 'where' : 'orWhere';
                        $query->{$action}($column, 'like', '%' . $keword . '%');
                    }
                });
            } elseif (count($sections) == 2) {

                $zone = trim($sections[0]);
                $keword = trim($sections[1]);

                if (in_array($zone, $querying)) {

                    switch ($zone) {

                        case 'state':
                            $model = $model->where('state', $keword == 'true' ? 1 : 0);
                            break;
                        case 'order_by':
                            $keyword_part = explode('/', $keword);
                            if (count($keyword_part) == 1) {
                                $column = 'id';
                                $direction = in_array(trim($keyword_part[0]), ['desc', 'asc']) ?
                                    trim($keyword_part[0]) :
                                    'asc';
                            } elseif (count($keyword_part) == 2) {
                                $column = trim($keyword_part[0]);
                                $direction = trim($keyword_part[1]);
                                $direction = in_array($direction, ['desc', 'asc']) ?
                                    $direction :
                                    'asc';
                            }
                            $model = $model->orderBy($column, $direction);
                            break;
                        case 'created_at':
                            $keyword_part = explode('/', $keword);
                            if (count($keyword_part) == 1) {
                                $model = $model->whereDate('created_at', trim($keyword_part[0]));
                            } else {
                                $model = $model->whereBetween(
                                    'created_at',
                                    [trim($keyword_part[0]), trim($keyword_part[1])]
                                );
                            }
                            break;
                        case 'expired_at':
                            $keyword_part = explode('/', $keword);
                            if (count($keyword_part) == 1) {
                                $model = $model->whereDate('expired_at', trim($keyword_part[0]));
                            } else {
                                $model = $model->whereBetween(
                                    'expired_at',
                                    [trim($keyword_part[0]), trim($keyword_part[1])]
                                );
                            }
                            break;
                        case 'random':
                            if (trim($keword) == 'true') {
                                $model = $model->inRandomOrder();
                            }
                            break;
                        case 'limit':
                            $keyword_part = explode('/', $keword);
                            if (count($keyword_part) == 1) {
                                $model = $model->take(intval(trim($keyword_part[0])));
                            } elseif (count($keyword_part) == 2) {
                                $model = $model->offset(intval(trim($keyword_part[1])))
                                    ->limit(intval(trim($keyword_part[0])));
                            }
                            break;
                        default:
                            $kewords = explode(',', $keword);
                            $model = $model->whereIn($zone, $kewords);
                            break;
                    }
                } elseif (in_array($zone, array_keys($relations))) {

                    $kewords = explode(',', $keword);
                    $relation = $relations[$zone] ?? [];

                    $method = $relation['method'] ?? null;
                    $table = $relation['table'] ?? null;
                    $columns = isset($relation['columns']) ? explode(',', $relation['columns']) : null;

                    if (!empty($kewords) && isset($method) && !empty($columns)) {

                        $model = $model->whereHas($method, function ($query) use ($kewords, $table, $columns) {

                            foreach ($columns as $key => $column) {

                                $action = $key == 0 ? 'whereIn' : 'orWhereIn';
                                $query->{$action}($table . '.' . trim($column), $kewords);
                            }
                        });
                    }
                }
            }
        }

        return $model;
    }

    /**
     * moduloDatas
     * 
     * Get module datas based on the provided options in Laravel project.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param array $options An associative array with the following keys:
     *   - 'modules': A string or array of module names to fetch data for.
     *   - 'namespace': An optional string specifying the namespace of the module controllers.
     *   - 'authorized': An optional array of authorized module names. Defaults to an empty array.
     *   - 'regex_modules': An optional regular expression pattern for matching module names.
     *   - 'regex_matches': An optional associative array specifying the regex matches for the module names.
     *   - 'actions': An optional array of actions to fetch data for. Defaults to ['list', 'detail'].
     *   - 'filters': An optional array of filters for the modules. Defaults to an empty array.
     *   - 'pages': An optional array of page numbers for the modules. Defaults to an empty array.
     *   - 'plurals': An optional associative array specifying the plural forms for the module names.
     *   - 'results_keys': An optional associative array specifying the keys for the module results.
     *
     * @return array A associative array containing the following keys:
     *   - 'modules': An associative array containing the fetched module data.
     *   - 'paginate': An associative array containing the pagination data for the modules.
     */
    static function moduloDatas($request, array $options = []): array
    {

        /**
         * Initializing options
         */
        $modules = $options['modules'] ?? $request->modules;
        $namespace = $options['namespace'] ?? 'All';
        $authorized = $options['authorized'] ?? [];
        $regex_modules = $options['regex_modules'] ?? '/^(.*)\_(list|detail)(?:[:](.*))?$/i';
        $regex_matches = $options['regex_matches'] ?? ['module' => 1, 'action' => 2, 'item' => 3];
        $actions = $options['actions'] ?? ['list', 'detail'];
        $filters = $options['filters'] ?? [];
        $pages = $options['pages'] ?? [];
        $plurals = $options['plurals'] ?? [];
        $results_keys = $options['results_keys'] ?? [];

        $modules = explode('|', $modules);

        $list = [];
        $paginate = [];

        foreach ($modules as $module) {

            preg_match($regex_modules, $module, $matches);

            if (count($matches) != 3 && count($matches) != 4) {
                continue;
            }

            $module = $matches[$regex_matches['module']];
            $action = $matches[$regex_matches['action']];
            $item = $matches[$regex_matches['item']] ?? '';

            if (in_array($module, $authorized)) {

                /**
                 * Module controller namespace
                 */
                $controllerNamespace = 'App\\Http\\Controllers\\' . $namespace . '\\' . ucfirst($module);
                $controllerClass = $controllerNamespace . '\\' . ucfirst($module) . 'Controller';

                if (
                    class_exists($controllerClass) &&
                    in_array($action, $actions)
                ) {

                    /**
                     * Injection of the filler and the page for the module
                     */
                    $request->merge([
                        'filter' => $filters[$module] ?? $request->input('filter_' . $module),
                        'page' => $pages[$module] ?? $request->input('page_' . $module),
                        'per_page' => $request->input('per_page_' . $module),
                    ]);

                    /**
                     * Controller initialization
                     */
                    $Controller = new $controllerClass();

                    /**
                     * Initializing the controller method
                     */
                    $controllerAction = 'post' . ucfirst($action);

                    /**
                     * Data recovery
                     */
                    $datas = $Controller->{$controllerAction}($request, $item);
                    $datas = $datas->getData(true);

                    if (isset($datas['results']) && !empty($datas['results'])) {

                        $results = $datas['results'];

                        if ($action == 'list') {

                            $modulKey = $plurals[$module] ?? $module . 's';

                            if (isset($datas['paginate'])) {
                                $paginate[$module] = $datas['paginate'] ?? [];
                            }
                        } else {
                            $modulKey = $module;
                        }

                        if (isset($results_keys[$module])) {

                            $key = $results_keys[$module];

                            if ($key == '.') {
                                $list[$modulKey] =  $results;
                            } elseif (isset($results[$key])) {
                                $list[$modulKey] =  $results[$key];
                            }
                        } else {
                            $list[$modulKey] =  $results[$modulKey] ?? [];
                        }
                    }
                }
            }
        }

        return [
            'modules' => $list,
            'paginate' => $paginate,
        ];
    }

    /**
     * Sends a response with the specified results, paginate, and messages.
     *
     * @param array $results The results to include in the response.
     * @param array $paginate The pagination information to include in the response.
     * @param array $messages The messages to include in the response.
     *
     * @return void
     */
    static function responseResults(array $results, array $paginate = [], array $messages = []): void
    {
        self::responseJson(true, [
            'results' => $results,
            'paginate' => $paginate,
            'messages' => $messages,
        ]);
    }

    /**
     * Sends an error response with the specified code, key, and message.
     *
     * @param int $code The error code. Defaults to 9999.
     * @param string $key   The error key.
     * @param string $msg   The error message. If neither $code nor $msg is specified, a default message is used.
     *
     * @return void
     */
    static function responseErrors(int $code = 9999, string $key = '', string $msg = ''): void
    {
        $msg = ($code != 9999 || !empty($msg)) ? $msg : "Oops! An error has occurred";
        self::responseJson(false, [
            'errors' => [
                'code' => $code,
                'key' => $key,
                'msg' => $msg,
            ]
        ]);
    }

    /**
     * This function sends a JSON response with the provided success status and data.
     *
     * @param bool $success Indicates whether the response is successful or not.
     * @param array $datas An associative array containing the data to be included in the response.
     *                      The array should contain the following keys: 'esults', 'paginate', 'errors', 'essages'.
     *                      Defaults to an empty array for each key.
     *
     * @return void
     */
    static function responseJson(bool $success, array $datas = ['results' => [], 'paginate' => [], 'errors' => [], 'messages' => []]): void
    {

        $response = [
            'success' => $success,
            'results' => $datas['results'] ?? [],
            'paginate' => $datas['paginate'] ?? [],
            'errors' => $datas['errors'] ?? [],
            'messages' => $datas['messages'] ?? [],
        ];

        if (empty($response['paginate'])) {
            unset($response['paginate']);
        }

        header('Content-Type: application/json');

        echo json_encode($response);
        die();
    }
}
