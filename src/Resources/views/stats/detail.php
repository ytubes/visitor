<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Статистика посещений';
$this->params['subtitle'] = 'Детализация за сутки';
$this->params['breadcrumbs'][] = ['label' => 'Статистика посещений', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Детализация за сутки';

?>

<div class="row">
	<div class="col-md-12">

		<div class="box box-primary">
			<div class="box-header with-border">
				<i class="glyphicon glyphicon-list-alt" style="color: #d81b60;"></i><h3 class="box-title">Данные</h3>
				<div class="box-tools pull-right">
					<div class="btn-group">

					</div>
				</div>
            </div>

            <div class="box-body pad">

				<?= GridView::widget([
					'dataProvider' => $dataProvider,
					'layout' => "{pager}\n{summary}\n{items}\n{pager}",
					'columns' => [

				        [
				        	'attribute' => 'ip',
				        	'options' => [
								'style' => 'width:70px',
							],
				        ],
				        [
				        	'attribute' => 'first_visit',
				        	//'options' => [
							//	'style' => 'width:100px',
							//],
				        ],
				        [
				        	'attribute' => 'last_visit',
				        	'options' => [
								'style' => 'width:100px',
							],
				        ],
				        [
				        	'attribute' => 'session_time',
				            'value' => function ($data) {
				                return round((int) $data->session_time / 60, 1);
				            },
				        ],
				        [
				        	'attribute' => 'raw_in',
				        ],
				        [
				        	'attribute' => 'views',
				        ],
				        [
				        	'attribute' => 'clicks',
				        ],
				        [
				        	'attribute' => 'ref_site',
				        ],
				        [
				        	'attribute' => 'ref_group',
				        ],
				        [
				        	'attribute' => 'device_group',
				        ],
					],
				]); ?>

			</div>
		</div>

	</div>
</div>
