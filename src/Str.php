<?php


namespace INTCore\OneARTConsole;

use Illuminate\Support\Str as StrHelper;

class Str
{
    /**
     * Determine the real name of the given name,
     * excluding the given pattern.
     *    i.e. the name: "CreateArticleFeature.php" with pattern '/Feature.php'
     *        will result in "Create Article".
     *
     * @param string $name
     * @param string $pattern
     *
     * @return string
     */
    public static function realName($name, $pattern = '//')
    {
        $name = preg_replace($pattern, '', $name);

        return implode(' ', preg_split('/(?=[A-Z])/', $name, -1, PREG_SPLIT_NO_EMPTY));
    }

    /**
     * Get the given name formatted as a feature.
     *
     *    i.e. "Create Post Feature", "CreatePostFeature.php", "createPost", "createe"
     *    and many other forms will be transformed to "CreatePostFeature" which is
     *    the standard feature class name.
     *
     * @param string $name
     * @return string
     */
    public static function feature($name)
    {
        $name = static::getClassNameWithoutNamespace($name);

        return StrHelper::studly(preg_replace('/Feature(\.php)?$/', '', $name) . 'Feature');
    }

    /**
     * Get the given name formatted as a job.
     *
     *    i.e. "Create Post Feature", "CreatePostJob.php", "createPost",
     *    and many other forms will be transformed to "CreatePostJob" which is
     *    the standard job class name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function job($name)
    {
        $name = static::getClassNameWithoutNamespace($name);

        return StrHelper::studly(preg_replace('/Job(\.php)?$/', '', $name) . 'Job');
    }

    /**
     * Get the given name formatted as a event.
     *
     *    i.e. "Create Order Created Order", "OrderCreatedEvent.php", "createOrder",
     *    and many other forms will be transformed to "OrderCreatedEvent" which is
     *    the standard event class name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function event($name)
    {
        $name = static::getClassNameWithoutNamespace($name);

        return StrHelper::studly(preg_replace('/Event(\.php)?$/', '', $name) . 'Event');
    }

    /**
     * Get the given name formatted as a listener.
     *
     *    i.e. "Create Order", "OrderCreatedListener.php", "createOrder",
     *    and many other forms will be transformed to "OrderCreatedListener" which is
     *    the standard listener class name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function listener(string $name)
    {
        $name = static::getClassNameWithoutNamespace($name);

        return StrHelper::studly(preg_replace('/Listener(\.php)?$/', '', $name) . 'Listener');
    }




    /**
     * Get the given name formatted as an operation.
     *
     *  i.e. "Create Post Operation", "CreatePostOperation.php", "createPost",
     *  and many other forms will be transformed to "CreatePostOperation" which is
     *  the standard operation class name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function operation($name)
    {
        return StrHelper::studly(preg_replace('/Operation(\.php)?$/', '', $name) . 'Operation');
    }

    /**
     * @DEPRICATED
     * Get the given name formatted as a service name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function service($name)
    {
        return StrHelper::studly($name);
    }

    /**
     * Get the given name formatted as a service name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function domain($name)
    {
        return StrHelper::studly($name);
    }

    /**
     * Get the given name formatted as a controller name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function controller($name)
    {
        $name = static::getClassNameWithoutNamespace($name);
        return StrHelper::studly(preg_replace('/Controller(\.php)?$/', '', $name) . 'Controller');
    }

    /**
     * remove any slashes of class name | use
     * @param string $name
     * @return string
     */
    public static function getClassNameWithoutNamespace(string $name)
    {
        # has / decimals
        if(count(explode('/', $name)) > 1) {
            $namespaces = explode('/', $name);
            $name = end($namespaces);
        }

        return $name;
    }

    /**
     * Get the given name formatted as a model.
     *
     * Model names are just CamelCase
     *
     * @param string $name
     *
     * @return string
     */
    public static function model($name)
    {
        $name = static::getClassNameWithoutNamespace($name);

        return StrHelper::studly($name);
    }

    /**
     * Get the given name formatted as a policy.
     *
     * @param $name
     * @return string
     */
    public static function policy($name)
    {
        $name = static::getClassNameWithoutNamespace($name);

        return StrHelper::studly(preg_replace('/Policy(\.php)?$/', '', $name) . 'Policy');
    }

    /**
     * Get the given name formatted as a request.
     *
     * @param $name
     * @return string
     */
    public static function request($name)
    {
        $name = static::getClassNameWithoutNamespace($name);

        return StrHelper::studly(preg_replace('/Request(\.php)?$/', '', $name) . 'Request');
    }

    /**
     * Get the given name formatted as a mail.
     *
     *    i.e. "Create Welcome Email", "WelcomeEmail.php", "Welcome",
     *    and many other forms will be transformed to "WelcomeEmail" which is
     *    the standard job class name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function email($name)
    {
        $name = static::getClassNameWithoutNamespace($name);

        return StrHelper::studly(preg_replace('/Mails(\.php)?$/', '', $name) . 'Mail');
    }

    /**
     * Get the given name formatted as a notification.
     *
     *    i.e. "Create Notification Email", "NotificationEmail.php", "Notification",
     *    and many other forms will be transformed to "NotificationEmail" which is
     *    the standard job class name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function notification($name)
    {
        $name = static::getClassNameWithoutNamespace($name);

        return StrHelper::studly(preg_replace('/Notifications(\.php)?$/', '', $name) . 'Notifications');
    }

    /**
     * Get the given name formatted as a Resource.
     *
     *    i.e. "Create Post Resource", "PostResource.php", "createPost",
     *    and many other forms will be transformed to "PostResource" which is
     *    the standard job class name.
     *
     * @param string $name
     * @param $is_collection
     * @return string
     */
    public static function resource($name, $is_collection)
    {
        //        $suffix = $is_collection ? "Collection" : "Resource";
        //        $name .= $suffix;
        $name = static::getClassNameWithoutNamespace($name);
        return StrHelper::studly(preg_replace('/Resources(\.php)?$/', '', $name));
    }


    /**
     * Get the given name formatted as a listener.
     *
     *    i.e. "User Repository", "UserRepository.php",
     *    and many other forms will be transformed to "UserRepository" which is
     *    the standard repository class name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function repository(string $name)
    {
        $name = static::getClassNameWithoutNamespace($name);

        return StrHelper::studly(preg_replace('/Repository(\.php)?$/', '', $name) . 'Repository');
    }

}
