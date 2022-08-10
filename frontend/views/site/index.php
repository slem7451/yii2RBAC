<?php

/** @var yii\web\View $this */

/** @var common\models\User $users */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'My Yii Application';
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
$user_role = 'client' . 'Moderator';
if (Yii::$app->authManager->getAssignment('outsource', Yii::$app->user->id))
    $user_role = 'outsource' . 'Moderator';
?>
<div class="site-index">

    <?php
    if (array_key_exists('admin', $roles))
        echo 'Вы админ. Вы зашли через frontend часть, чтобы перейти к списку пользователей, авторизуйтесь в ' . Html::a('backend.', Url::to('http://localhost:21080/', true));
    else {
        if (Yii::$app->user->can('clientCRUD'))
            echo Html::a('Создать клиент модератора', array('site/create'), array('class' => 'btn btn-primary pull-right'));
        if (Yii::$app->user->can('outsourceCRUD'))
            echo Html::a('Создать оутсорс модератора', array('site/create'), array('class' => 'btn btn-primary pull-right'));
    } ?>
    <?php if ((Yii::$app->user->can('clientCRUD') || Yii::$app->user->can('outsourceCRUD')) && !array_key_exists('admin', $roles)): ?>
        <div class="clearfix"></div>
        <table class="table table-striped table-hover">
            <tr>
                <td>Id</td>
                <td>Username</td>
                <td>Options</td>
            </tr>
            <?php foreach ($users as $post): ?>
                <tr>
                    <?php
                    if (Yii::$app->authManager->getAssignment($user_role, $post->id)):
                        ?>
                        <td>
                            <?php echo $post->id; ?>
                        </td>
                        <td><?php echo $post->username; ?></td>
                        <td>
                            <?php echo Html::a('Редактировать', ['site/update', 'id' => $post->id]); ?>
                            <?php
                            if ($post->status === 10)
                                echo Html::a('Заблокировать', ['site/block', 'id' => $post->id]);
                            else
                                echo Html::a('Разблокировать', ['site/unblock', 'id' => $post->id]); ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
