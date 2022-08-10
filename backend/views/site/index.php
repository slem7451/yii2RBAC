<?php

/** @var yii\web\View $this */
/** @var common\models\User $users */
use yii\helpers\Html;

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <div class="clearfix"></div>
    <table class="table table-striped table-hover">
        <tr>
            <td>Id</td>
            <td>Username</td>
            <td>Options</td>
        </tr>
        <?php foreach ($users as $post): ?>
            <tr>
                <td>
                    <?php echo $post->id; ?>
                </td>
                <td><?php echo $post->username; ?></td>
                <td>
                    <?php echo Html::a('Редактировать', ['site/update', 'id'=>$post->id]); ?>
                    <?php
                    if ($post->status === 10)
                        echo Html::a('Заблокировать', ['site/block', 'id'=>$post->id]);
                    else
                        echo Html::a('Разблокировать', ['site/unblock', 'id'=>$post->id]);?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
