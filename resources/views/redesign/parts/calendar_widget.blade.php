<div class="calendar-widget">
	<div class="orders_now__title font-weight-bold">
		<span>{{\Carbon\Carbon::now()->format('d.m')}} - {{\Carbon\Carbon::now()->addMonth()->format('d.m')}}</span>
	</div>
	<div class="plane_table">
		<table>
			<thead>
				<tr>
					<th>
						Пн
					</th>
					<th>
						Вт
					</th>
					<th>
						Ср
					</th>
					<th>
						Чт
					</th>
					<th>
						Пт
					</th>
					<th>
						Сб
					</th>
					<th>
						Вс
					</th>
				</tr>
			</thead>
			<tbody>

				<?php $days_all = App\Helpers\Calendar::getCalendar(true);?>
				@for($i=0; $i<=\Carbon\Carbon::now()->daysInMonth; $i++)
					<?php
					$d = strtotime(date('Y-m-d') . "+$i day");
					$dat = date('Y-m-d', $d);
					?>

					@foreach ($days_all as $key => $days_all_mon )
						@foreach($days_all_mon as $key2 =>  $days_all_wk )
							@foreach($days_all_wk as $key3 =>  $data_day)
								@if($data_day['date'] == $dat)
									<?php $arr_wk["$key2"]["$key3"] = $data_day;?>
								@endif
							@endforeach
						@endforeach
					@endforeach
				@endfor

				{{$isSet = false}}
				@foreach($arr_wk as $wk)
					<tr>
						@foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
							@if(isset($wk[$day]))
								<?php $class_date = ''; if (strtotime($wk[$day]['date']) == strtotime(\Carbon\Carbon::now()
								                                                                                    ->format('Y-m-d')) && !$isSet) {
									$class_date = 'active_day';
									$isSet = true;
								}?>
								@if(($wk[$day]['delivery']))
									<td>
										<span class='delivery_day <?php echo isset($class_date) ? $class_date : ''?>'><img src="/images/icons/delivery_day__icon_15x15.png"/></span>
									</td>
								@elseif($day == 'Sun')
									<td>
										<span class='weekend_day <?php echo isset($class_date) ? $class_date : ''?> '>{{ $wk[$day]['day'] }}</span>
									</td>
								@else
									<td>
										<span class='<?php echo isset($class_date) ? $class_date : ''?>'>{{ isset($wk[$day]) ? $wk[$day]['day'] : '' }}</span>
									</td>
								@endif
							@else
								<td>
									<span></span>
								</td>
							@endif
						@endforeach
					</tr>
				@endforeach
			</tbody>
		</table>
		<div class="date_table__description">
			<span>* <img src="/images/icons/delivery_day__icon_15x15.png"/> - дни привоза</span>
		</div>
	</div>
	@if(App\Helpers\Calendar::nextDelivery())
		<button class="orders_now__date">Ближайшая поставка<br>{{ App\Helpers\Calendar::nextDelivery() }}</button>
	@endif
</div>