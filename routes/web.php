<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group([
    'prefix' => 'artisan',
    'middleware' => ['auth'],
], function () {
    Route::get('optimize-clear', function() {
        Artisan::call('optimize:clear');
        return 'Application cache has been cleared';
    });
    Route::get('storage-link', function() {
        try {
            Artisan::call('storage:link --relative');
            return 'Symlink успешно создан!';
        } catch (\Exception $e) {
            return 'Ошибка: ' . $e->getMessage();
        }
    });
    Route::get('/storage-link-standard', function () {
        // Стандартная структура Laravel, где public внутри проекта
        $target = base_path('storage/app/public');
        $link = public_path('storage');
        
        // Удаляем файл storage, если он уже существует
        if (file_exists($link) && !is_dir($link) && !is_link($link)) {
            unlink($link);
        }
        
        // Показываем информацию о путях
        echo "Target path: $target<br>";
        echo "Link path: $link<br>";
        
        // Проверяем существование исходной папки
        if (!file_exists($target)) {
            return "Ошибка: папка-источник ($target) не существует!";
        }
        
        try {
            // Стандартная команда artisan
            Artisan::call('storage:link');
            return "Symlink успешно создан!<br>$target -> $link";
        } catch (\Exception $e) {
            // Если artisan не сработал, пробуем прямой symlink
            try {
                symlink($target, $link);
                return "Symlink создан вручную!<br>$target -> $link";
            } catch (\Exception $e2) {
                return "Ошибки:<br>1. " . $e->getMessage() . "<br>2. " . $e2->getMessage();
            }
        }
    });
    
    Route::get('/storage-link-symfony', function () {
        $filesystem = new Filesystem();
        
        $target = base_path('storage/app/public');
        $link = public_path('storage');
        
        echo "Target path: $target<br>";
        echo "Link path: $link<br>";
        
        // Проверяем существование исходной папки
        if (!file_exists($target)) {
            return "Ошибка: папка-источник ($target) не существует!";
        }
        
        // Удаляем файл storage, если он существует и не симлинк
        if (file_exists($link) && !is_link($link)) {
            if (is_dir($link)) {
                $filesystem->remove($link);
            } else {
                unlink($link);
            }
        }
        
        try {
            // Создаем симлинк с помощью Symfony Filesystem
            $filesystem->symlink($target, $link);
            return "Symlink успешно создан с помощью Symfony Filesystem!<br>$target -> $link";
        } catch (IOExceptionInterface $e) {
            return "Ошибка: " . $e->getMessage();
        }
    });

    // Метод, который создаёт .htaccess для перенаправления вместо симлинка
    Route::get('/storage-link-htaccess', function () {
        // Пути для работы на сервере
        $storage_path = storage_path('app/public');
        $public_storage_path = public_path('storage');
        
        echo "Полный путь к storage/app/public: $storage_path<br>";
        echo "Полный путь к public/storage: $public_storage_path<br>";
        
        // Проверяем существование исходной папки
        if (!file_exists($storage_path)) {
            // Пробуем создать папку
            if (!mkdir($storage_path, 0755, true)) {
                return "Ошибка: не удалось создать папку ($storage_path)!";
            }
        }
        
        // Создаём папку storage в public, если её нет
        if (!file_exists($public_storage_path)) {
            if (!mkdir($public_storage_path, 0755, true)) {
                return "Ошибка: не удалось создать папку ($public_storage_path)!";
            }
        }
        
        // Создаём тестовый файл в storage/app/public
        $test_file_content = "Тестовый файл для проверки доступа к storage";
        $test_file_path = $storage_path . '/test.txt';
        file_put_contents($test_file_path, $test_file_content);
        echo "Создан тестовый файл: $test_file_path<br>";
        
        // Определяем относительный путь для .htaccess
        $relative_path = '/storage/app/public';
        
        // Создаём .htaccess файл в папке public/storage
        $htaccess_content = <<<EOT
# Правила для перенаправления запросов к storage
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /storage/
    
    # Проверяем, не существует ли файл или директория
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    # Перенаправляем к файлам в storage/app/public
    RewriteRule ^(.*)$ $relative_path/$1 [L]
</IfModule>

# Альтернативный вариант, если mod_rewrite не работает
<IfModule !mod_rewrite.c>
    RedirectMatch 302 ^/storage/(.*)$ $relative_path/$1
</IfModule>
EOT;
        
        $htaccess_file = $public_storage_path . '/.htaccess';
        
        if (file_put_contents($htaccess_file, $htaccess_content)) {
            echo "Успешно создан .htaccess для перенаправления запросов!<br>";
            
            // Создаём тестовый HTML файл в public/storage для проверки
            $html_test = <<<HTML
<!DOCTYPE html>
<html>
<head><title>Storage Test</title></head>
<body>
    <h1>Storage Test</h1>
    <p>Эта страница находится в папке /public/storage</p>
    <p>Тестовый файл должен быть доступен по ссылке: <a href="test.txt">test.txt</a></p>
</body>
</html>
HTML;
            
            file_put_contents($public_storage_path . '/index.html', $html_test);
            
            return "
                <p>Настройка завершена! Проверьте доступность файлов:</p>
                <ul>
                    <li><a href='/storage/test.txt' target='_blank'>Проверить test.txt через /storage/</a></li>
                    <li><a href='/storage/app/public/test.txt' target='_blank'>Проверить test.txt напрямую</a></li>
                    <li><a href='/storage/index.html' target='_blank'>Проверить тестовую страницу</a></li>
                </ul>
                <p>Если ни один из вариантов не работает, обратитесь к хостинг-провайдеру с просьбой создать симлинк или проверить настройки Apache.</p>
            ";
        } else {
            return "Ошибка при создании .htaccess файла";
        }
    });

    Route::get('/storage-link-shared-hosting', function () {
        // Пути для работы на shared хостинге
        $storage_path = storage_path('app/public');
        $public_storage_path = public_path('storage');
        
        echo "Полный путь к storage/app/public: $storage_path<br>";
        echo "Полный путь к public/storage: $public_storage_path<br>";
        
        // Проверяем существование исходной папки
        if (!file_exists($storage_path)) {
            // Пробуем создать папку
            if (!mkdir($storage_path, 0755, true)) {
                return "Ошибка: не удалось создать папку ($storage_path)!";
            }
        }
        
        // Удаляем файл storage, если он существует и не директория
        if (file_exists($public_storage_path) && !is_dir($public_storage_path)) {
            unlink($public_storage_path);
            echo "Удалён существующий файл storage<br>";
        }
        
        // Создаём папку storage в public, если её нет
        if (!file_exists($public_storage_path)) {
            if (!mkdir($public_storage_path, 0755, true)) {
                return "Ошибка: не удалось создать папку ($public_storage_path)!";
            }
            echo "Создана директория public/storage<br>";
        }
        
        // Создаём тестовый файл в storage/app/public
        $test_file_content = "Тестовый файл для проверки доступа к storage";
        $test_file_path = $storage_path . '/test.txt';
        file_put_contents($test_file_path, $test_file_content);
        echo "Создан тестовый файл: $test_file_path<br>";
        
        // Создаём .htaccess файл в папке public/storage
        $htaccess_content = <<<EOT
# Правила для перенаправления запросов к storage
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /storage/
    
    # Не перенаправлять существующие файлы и директории
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    # Абсолютный путь к хранилищу через переменную окружения
    RewriteRule ^(.*)$ /storage/app/public/$1 [L]
</IfModule>
EOT;
        
        $htaccess_file = $public_storage_path . '/.htaccess';
        
        if (file_put_contents($htaccess_file, $htaccess_content)) {
            echo "Успешно создан .htaccess для перенаправления запросов!<br>";
            
            // Создаём тестовый HTML файл в public/storage для проверки
            $html_test = <<<HTML
<!DOCTYPE html>
<html>
<head><title>Storage Test</title></head>
<body>
    <h1>Storage Test</h1>
    <p>Эта страница находится в папке /public/storage</p>
    <p>Тестовый файл должен быть доступен по ссылке: <a href="test.txt">test.txt</a></p>
</body>
</html>
HTML;
            
            file_put_contents($public_storage_path . '/index.html', $html_test);
            
            return "
                <p>Настройка завершена! Проверьте доступность файлов:</p>
                <ul>
                    <li><a href='/storage/test.txt' target='_blank'>Проверить test.txt через /storage/</a></li>
                    <li><a href='/storage/index.html' target='_blank'>Проверить тестовую страницу</a></li>
                </ul>
            ";
        } else {
            return "Ошибка при создании .htaccess файла";
        }
    });

    // Команда для автоматического поиска и обновления всех URL в базе
    Route::get('/fix-all-urls', function () {
        $oldDomain = 'ogurchik.local';
        $newDomain = 'dacha-gid.ru';
        
        $oldUrlHttp = "http://{$oldDomain}";
        $oldUrlHttps = "https://{$oldDomain}";
        $newUrl = "https://{$newDomain}";
        
        $totalUpdated = 0;
        $results = [];
        
        try {
            // Получаем все таблицы в базе данных
            $tables = DB::select('SHOW TABLES');
            $databaseName = DB::getDatabaseName();
            $tableColumn = "Tables_in_{$databaseName}";
            
            foreach ($tables as $table) {
                $tableName = $table->$tableColumn;
                
                // Получаем все TEXT/VARCHAR поля таблицы
                $columns = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Type LIKE '%text%' OR Type LIKE '%varchar%'");
                
                foreach ($columns as $column) {
                    $fieldName = $column->Field;
                    
                    // Проверяем, есть ли в этом поле старые URL
                    $count = DB::table($tableName)
                        ->where($fieldName, 'LIKE', "%{$oldDomain}%")
                        ->count();
                    
                    if ($count > 0) {
                        // Обновляем HTTP URL
                        $updated1 = DB::table($tableName)
                            ->whereRaw("`{$fieldName}` LIKE ?", ["%{$oldUrlHttp}%"])
                            ->update([
                                $fieldName => DB::raw("REPLACE(`{$fieldName}`, '{$oldUrlHttp}', '{$newUrl}')")
                            ]);
                        
                        // Обновляем HTTPS URL
                        $updated2 = DB::table($tableName)
                            ->whereRaw("`{$fieldName}` LIKE ?", ["%{$oldUrlHttps}%"])
                            ->update([
                                $fieldName => DB::raw("REPLACE(`{$fieldName}`, '{$oldUrlHttps}', '{$newUrl}')")
                            ]);
                        
                        $updated = $updated1 + $updated2;
                        $totalUpdated += $updated;
                        
                        $results[] = "Таблица: {$tableName}, поле: {$fieldName} - обновлено {$updated} записей";
                    }
                }
            }
            
            // Очищаем кэш
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            
            $output = "<h3>Результаты обновления URL:</h3>";
            $output .= "<p><strong>Всего обновлено записей: {$totalUpdated}</strong></p>";
            $output .= "<ul>";
            foreach ($results as $result) {
                $output .= "<li>{$result}</li>";
            }
            $output .= "</ul>";
            $output .= "<p>Кэш очищен.</p>";
            
            return $output;
            
        } catch (\Exception $e) {
            return "Ошибка: " . $e->getMessage() . "<br>Файл: " . $e->getFile() . "<br>Строка: " . $e->getLine();
        }
    });

    // Команда для предварительного просмотра записей с неправильными URL
    Route::get('/preview-url-issues', function () {
        $oldDomain = 'ogurchik.local';
        $results = [];
        
        try {
            // Получаем все таблицы в базе данных
            $tables = DB::select('SHOW TABLES');
            $databaseName = DB::getDatabaseName();
            $tableColumn = "Tables_in_{$databaseName}";
            
            foreach ($tables as $table) {
                $tableName = $table->$tableColumn;
                
                // Получаем все TEXT/VARCHAR поля таблицы
                $columns = DB::select("SHOW COLUMNS FROM `{$tableName}` WHERE Type LIKE '%text%' OR Type LIKE '%varchar%'");
                
                foreach ($columns as $column) {
                    $fieldName = $column->Field;
                    
                    // Ищем записи со старыми URL
                    $records = DB::table($tableName)
                        ->select('id', $fieldName)
                        ->where($fieldName, 'LIKE', "%{$oldDomain}%")
                        ->limit(10) // Ограничиваем количество для примера
                        ->get();
                    
                    if ($records->count() > 0) {
                        $results[] = [
                            'table' => $tableName,
                            'field' => $fieldName,
                            'count' => DB::table($tableName)->where($fieldName, 'LIKE', "%{$oldDomain}%")->count(),
                            'examples' => $records
                        ];
                    }
                }
            }
            
            $output = "<h3>Найденные проблемы с URL:</h3>";
            
            if (empty($results)) {
                $output .= "<p style='color: green;'>✅ Проблемных URL не найдено!</p>";
            } else {
                foreach ($results as $result) {
                    $output .= "<div style='margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;'>";
                    $output .= "<h4>Таблица: {$result['table']}, Поле: {$result['field']}</h4>";
                    $output .= "<p><strong>Всего записей с проблемами: {$result['count']}</strong></p>";
                    $output .= "<h5>Примеры (до 10 записей):</h5>";
                    $output .= "<ul>";
                    
                    foreach ($result['examples'] as $example) {
                        $content = mb_strlen($example->{$result['field']}) > 100 
                            ? mb_substr($example->{$result['field']}, 0, 100) . '...' 
                            : $example->{$result['field']};
                        
                        $output .= "<li><strong>ID {$example->id}:</strong> " . htmlspecialchars($content) . "</li>";
                    }
                    
                    $output .= "</ul>";
                    $output .= "</div>";
                }
                
                $output .= "<p style='color: orange;'>⚠️ Найдены проблемы! Используйте <a href='/artisan/fix-all-urls'>fix-all-urls</a> для исправления.</p>";
            }
            
            return $output;
            
        } catch (\Exception $e) {
            return "Ошибка: " . $e->getMessage() . "<br>Файл: " . $e->getFile() . "<br>Строка: " . $e->getLine();
        }
    });
});

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/pages/check-non-standard', [PageController::class, 'findNonStandardCharacters'])->name('pages.check-non-standard');


// Маршрут для страниц
Route::get('/pages/{page:slug}', [PageController::class, 'show'])->name('pages.show');

Route::get('/new', [PageController::class, 'new'])->name('new');
Route::get('/random', [PageController::class, 'random'])->name('random');
Route::get('/search', [PageController::class, 'search'])->name('search');
