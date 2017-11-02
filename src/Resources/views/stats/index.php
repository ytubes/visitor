<?php

use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Статистика посещений';
$this->params['subtitle'] = 'Сводный отчет';

//$this->params['breadcrumbs'][] = ['label' => 'Категории видео', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Статистика посещений';

?>

<div class="row">
	<div class="col-md-12">

		<div class="box box-warning">
			<div class="box-header with-border">
				<i class="fa fa-area-chart" style="color: #d81b60;"></i><h3 class="box-title">Суточный отчет</h3>
				<div class="box-tools pull-right">
					<div class="btn-group">

					</div>
				</div>
            </div>

            <div class="box-body pad">

				<table class="table table-bordered">

					<tr>
						<th>Тип</th>
						<th>Raw in</th>
						<th>Unique in</th>
						<th>Clicks</th>
						<th>Views</th>
						<th>Просмотры на посетителя</th>
						<th>Время на сайте (мин)</th>
						<th>Продуктивность</th>
					</tr>

					<tr>
						<td>Last Hour</td>
						<td><?= $data['last_hour']['raw_in'] ?></td>
						<td><?= $data['last_hour']['total'] ?></td>
						<td><?= $data['last_hour']['clicks'] ?></td>
						<td><?= $data['last_hour']['views'] ?></td>
						<td><?= $data['last_hour']['views_per_user'] ?></td>
						<td><?= $data['last_hour']['session_time'] ?></td>
						<td><?= $data['last_hour']['productivity_total'] ?>%</td>
					</tr>

					<tr>
						<td>24 hour</td>
						<td><?= $data['last_day']['raw_in'] ?></td>
						<td><?= $data['last_day']['unique_in'] ?></td>
						<td><?= $data['last_day']['clicks'] ?></td>
						<td><?= $data['last_day']['views'] ?></td>
						<td><?= $data['last_day']['views_per_user'] ?></td>
						<td><?= $data['last_day']['session_time'] ?></td>
						<td><?= $data['last_day']['productivity_total'] ?>%</td>
					</tr>

					<tr>
						<td colspan="7">
							<span style="display:inline-block; margin-right:10px">Unique ratio: <?= $data['last_day']['unique_rate'] ?>%</span>
							<span style="display:inline-block; margin-right:10px">Отказы: <?= isset($data['last_day']['bounce_rate']) ? $data['last_day']['bounce_rate'] : 0 ?>%</span>
							<span style="display:inline-block; margin-right:10px">СЕ: <?= isset($data['se_percent']) ? $data['se_percent'] : 0 ?>%</span>
							<span style="display:inline-block; margin-right:10px">Без рефа(букмарки): <?= isset($data['bookmarks_percent']) ? $data['bookmarks_percent'] : 0 ?>%</span>
							<span style="display:inline-block; margin-right:10px">Телефоны: <?= isset($data['mobile_percent']) ? $data['mobile_percent'] : 0 ?>%</span>
						</td>
					</tr>

				</table>

			</div>


			<div class="box-footer clearfix">
			    <div class="form-group">
					<?= Html::a('<i class="fa fa-refresh"></i> Обновить', ['index'], ['class' => 'btn btn-primary']) ?>
					<?= Html::a('<i class="glyphicon glyphicon-list-alt"></i> Детально', ['/visitor/stats/detail'], ['class' => 'btn btn-default', 'target' => '_blank']) ?>
				</div>
			</div>

		</div>

	</div>
</div>
