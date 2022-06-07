<?php
/*
 * This function redirects the user to a page.
 */

use core\router\App;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

function redirect($path)
{
    header("Location: /{$path}");
}

/*
 * This function returns the view of a page.
 */

function tView($name, $data = [])
{
    extract($data);

    $path = "app/views/";
    $ext = ".view.twig";

    $loader = new FilesystemLoader('app/views');
    $twig = new Environment($loader, [
        'debug' => true,
    ]);
    $twig->addExtension(new DebugExtension());

    if (!file_exists("$path{$name}$ext")) {
        header('HTTP/1.0 404 Not Found');
        return iView("error/404");
    } else {
        try {
            return $twig->render("{$name}$ext", $data);
        } catch (LoaderError $e) {
            App::logError('There was a Twig LoaderError Exception. Details: ' . $e);
            header('HTTP/1.0 404 Twig LoaderError');
            return iView("error/404", ['error' => $e]);
        } catch (RuntimeError $e) {
            App::logError('There was a Twig RuntimeError Exception. Details: ' . $e);
            header('HTTP/1.0 404 wig RuntimeError');
            return iView("error/404", ['error' => $e]);
        } catch (SyntaxError $e) {
            App::logError('There was a Twig SyntaxError Exception. Details: ' . $e);
            header('HTTP/1.0 404 Twig SyntaxError');
            return iView("error/404", ['error' => $e]);
        }
    }
}


function iView($name, $data = [])
{
    extract($data);
    return require "app/views/{$name}.view.twig";
}

/*
 * This function is used for dark mode functionality,
 * it returns the first (dark) class string
 * or second (light class string).
 */
function theme($class, $secondClass)
{
    if (isset($_SESSION['darkmode']) && $_SESSION['darkmode'] == true) {
        return $class;
    }
    return $secondClass;
}

/*
 * This function is used for dying and dumping.
 */
function dd($value)
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}

/*
 * This function is used for generating pagination links.
 */
function paginate($table, $page, $limit, $count)
{
    $offset = ($page - 1) * $limit;
    $output = "<span class='" . theme('text-white-75', 'text-dark') . "'>";
    if ($page > 1) {
        $prev = $page - 1;
        $output .= "<a href='/{$table}/{$prev}' class='" . theme('text-light', 'text-primary') . "'>Prev</a>";
    }
    $output .= " Page $page ";
    if ($count > ($offset + $limit)) {
        $next = $page + 1;
        $output .= "<a href='/{$table}/{$next}' class='" . theme('text-light', 'text-primary') . "'>Next</a>";
    }
    $output .= "</span>";
    return $output;
}

/*
 * This function displays a session variable's value if it exists.
*/
function session($name)
{
    return $_SESSION[$name] ?? "";
}

/*
 * This function displays a session variable's value and unsets it if it exists.
 */
function session_once($name)
{
    if (isset($_SESSION[$name])) {
        $value = $_SESSION[$name];
        unset($_SESSION[$name]);
        return $value;
    }
    return "";
}

/*
 * This function enables displaying of errors in the web browser.
 */
function display_errors()
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
