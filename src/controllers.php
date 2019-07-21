<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addGlobal('user', $app['session']->get('user'));

    return $twig;
}));


$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html', [
        'readme' => file_get_contents('README.md'),
    ]);
});


$app->match('/login', function (Request $request) use ($app) {
    $username = $request->get('username');
    $password = $request->get('password');

    // Make sure they entered a username and password
    if ($username && $password) {

        // First check if the user exists in the database
        $sql = 'SELECT * FROM users WHERE username = ?';
        $user = $app['db']->fetchAssoc($sql, array($username));

        // If the user was found
        if ($user) {

            // Hash the password using Argon2i then check against the DB hash using a constant time verification
            $password_is_verified = password_verify($password, $user['password_hash']);

            // If correct, let them log in and see the todos
            if ($password_is_verified) {
                $app['session']->set('user', $user);
                return $app->redirect('/todo');
            }
        }
    }

    return $app['twig']->render('login.html', array());
});


$app->get('/logout', function () use ($app) {
    $app['session']->set('user', null);
    return $app->redirect('/');
});


$app->get('/todo/{id}', function ($id) use ($app) {
    if (null === $user = $app['session']->get('user')) {
        return $app->redirect('/login');
    }

    if ($id) {
        $sql = 'SELECT * FROM todos WHERE id = ?';
        $todo = $app['db']->fetchAssoc($sql, array((int) $id));

        return $app['twig']->render('todo.html', [
            'todo' => $todo,
        ]);
    } else {
        $sql = 'SELECT * FROM todos WHERE user_id = ?';
        $todos = $app['db']->fetchAll($sql, array((int) $user['id']));

        return $app['twig']->render('todos.html', [
            'todos' => $todos,
        ]);
    }
})
->value('id', null);


$app->post('/todo/add', function (Request $request) use ($app) {
    if (null === $user = $app['session']->get('user')) {
        return $app->redirect('/login');
    }

    $user_id = $user['id'];
    $description = $request->get('description');

    // Make sure the user added a description before adding
    if ($description) {
        $sql = 'INSERT INTO todos (user_id, description) VALUES (?, ?)';
        $app['db']->executeUpdate($sql, array((int) $user_id, $description));
    }

    // Otherwise return (ToDo: show error message in UI)
    return $app->redirect('/todo');
});


// Mark a todo item as complete
$app->match('/todo/complete/{id}', function ($id) use ($app) {
    if (null === $user = $app['session']->get('user')) {
        return $app->redirect('/login');
    }

    $sql = 'UPDATE todos SET completed = ? WHERE id = ?';
    $app['db']->executeUpdate($sql, array(1, (int) $id));

    return $app->redirect('/todo');
});


$app->match('/todo/delete/{id}', function ($id) use ($app) {
    
    // Don't allow deleting todos if not logged in
    if (null === $user = $app['session']->get('user')) {
        return $app->redirect('/login');
    }

    $sql = 'DELETE FROM todos WHERE id = ?';
    $app['db']->executeUpdate($sql, array((int) $id));

    return $app->redirect('/todo');
});
