<template>
	<v-layout row style="flex-wrap: wrap">
		<v-flex planing-calendar>
			<v-card class="calendars pa-4">
				<carousel :perPage="1"
				          :navigationEnabled="true"
				          :navigateTo="position">
					<slide v-for="(month, monthYear) in calendar"
					       :key="monthYear">
						<table class='planning_table'>
							<thead>
								<tr>
									<th colspan='7'>{{ monthName(monthYear) }}</th>
								</tr>
								<tr>
									<th>П</th>
									<th>В</th>
									<th>С</th>
									<th>Ч</th>
									<th>П</th>
									<th>С</th>
									<th>В</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="days in month">
									<td @click='changeActiveDay(days[day].date)'
                      style="cursor: pointer"
									    v-for="day in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']">
                      <span v-if="days[day]"
                            :class="{
                              'weekend_day': (day == 'Sun'),
                              'delivery_day': days[day].delivery,
                              'active_day': days[day].active
                            }">
                        <img v-if="days[day].delivery"
                             src="/images/icons/delivery_day__icon_15x15.png"
                             style='width: 15px'/>
                        <span v-else>{{ days[day].day }}</span>
                      </span>
									</td>
								</tr>
							</tbody>
						</table>
					</slide>
				</carousel>
			</v-card>
		</v-flex>
		<v-flex v-show='day'
		        planing-card>
			<v-card>
				<div>
					<v-card-title primary-title>
						<v-flex align-end>
							<span class='headline font-weight-medium'>{{ day.fullDate }}</span>
							<span v-if="day.delivery.date"
							      class="ml-2 grey--text subheading">(рейс)</span>
						</v-flex>
					</v-card-title>
					<v-list v-if="day.delivery.date && day.orderCount > 0">
						<v-list-tile d-flex
						             align-center>
							<span class='font-weight-medium'>Заказов:</span>
							<span class='ml-2 grey--text underline'>{{ day.orderCount }}</span>
						</v-list-tile>
						<v-list-tile d-flex
						             align-center>
							<span class='font-weight-medium'>Заказано товаров на сумму:</span>
							<span class='ml-2 grey--text underline'>{{ day.orderTotal }} руб.</span>
						</v-list-tile>
					</v-list>
				</div>

        <v-card-text v-if="day.delivery.date">
          <div class="row">
            <div class="col form-group">
              <div class="">Доставка по Минску С</div>
              <date-picker value-type="format"
                           format="DD.MM.YYYY"
                           v-model="day.delivery.minsk_date_from_edit"
              ></date-picker>
            </div>
            <div class="col form-group">
              <div class="">Доставка по Минску ПО</div>
              <date-picker value-type="format"
                           format="DD.MM.YYYY"
                           v-model="day.delivery.minsk_date_to_edit"
              ></date-picker>
            </div>
          </div>

          <div class="row">
            <div class="col form-group">
              <div class="">Доставка по РБ С</div>
              <date-picker value-type="format"
                           format="DD.MM.YYYY"
                           v-model="day.delivery.country_date_from_edit"
              ></date-picker>
            </div>
            <div class="col form-group">
              <div class="">Доставка по РБ ПО</div>
              <date-picker value-type="format"
                           format="DD.MM.YYYY"
                           v-model="day.delivery.country_date_to_edit"
              ></date-picker>
            </div>
          </div>

        </v-card-text>

				<v-card-actions class='d-flex'>
          <div class="col form-group" v-show="day.delivery.date && day.orderCount > 0">
            <div class="">Дата переноса</div>
            <date-picker value-type="format"
                         format="DD.MM.YYYY"
                         v-model="moveDeliveryDate"
            ></date-picker>
          </div>

					<v-btn @click="moveDelivery"
					       v-if="day.delivery.id && day.orderCount > 0"
					       color="warning">Перенести
					</v-btn>
					<v-btn @click="cancelDelivery"
					       v-if="day.delivery.id && day.orderCount == 0"
					       color="error">Отменить
					</v-btn>
					<v-btn @click="newDelivery"
					       v-if="currentDate"
					       color="success">
            {{ day.delivery.id ? 'Сохранить' : 'Запланировать'}}
					</v-btn>
				</v-card-actions>
			</v-card>
		</v-flex>
	</v-layout>
</template>


<script>
	import axios from '@/utils/axios'
	import {Carousel, Slide} from 'vue-carousel'
	import DateRangePicker   from 'vue2-daterange-picker'
  import DatePicker from 'vue2-datepicker'
  import 'vue2-datepicker/index.css';

	export default {
		name:       'PlanningPage',
		components: {
			Carousel,
			Slide,
			DateRangePicker,
      DatePicker
		},
		data() {
			return {
				position:         0,
				moveDeliveryDate: "",
				day: {
          fullDate: '',
          delivery: {
            date: '',
            status: 0,
            minsk_date_from: '',
            minsk_date_to: '',
            country_date_from: '',
            country_date_to: '',
          },
          orderCount: '',
          orderTotal: '',
        },
        minsk_date_from: "",
        minsk_date_to: "",
        country_date_from: "",
        country_date_to: "",
				currentDate:      false,
				calendar:         [],
				monthArray:       ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь',]
			}
		},
		methods:    {

			monthName(monthYear){
				let month, year;

				[year, month] = monthYear.split('-');

				return this.monthArray[month - 1] + ' ' + year;
			},

			loadPlanning() {
				axios.get('/api/planning').then((response) => {
					this.calendar = response.data.calendar;
					this.position = new Date().getMonth();
				})
			},

			changeActiveDay(date) {
				this.currentDate = date;
				axios.get('/api/planning/day/' + date).then((response) => {
					this.day = response.data.info

          this.day.delivery = this.day.delivery || {
            date: this.currentDate,
            status: 0,
            minsk_date_from: '',
            minsk_date_to: '',
            country_date_from: '',
            country_date_to: '',
          }
				})
			},

			newDelivery() {
				axios.post('/api/planning/save', {
				  date: this.day
        }).then(() => {
					this.loadPlanning();
					this.changeActiveDay(this.currentDate)
				})
			},

			cancelDelivery() {
				axios.get('/api/planning/cancel/' + this.currentDate).then(() => {
					this.loadPlanning();
					this.changeActiveDay(this.currentDate)
				})
			},

			moveDelivery() {
				let that = this;

				axios.get('/api/planning/move/' + this.currentDate + '/' + this.moveDeliveryDate).then(() => {
					this.loadPlanning();
					this.changeActiveDay(this.currentDate);
					this.moveDeliveryDate = '';
				})
			},

		},
		mounted() {
			this.loadPlanning();
		}
	}
</script>

<style>
	.planning_table {
		width: 100%;
		text-align: center;
	}

  .row {
    display: flex;
    margin-bottom: 20px;
  }

  .row .col {
    margin-left: 30px;
  }

  td:hover {
    text-decoration: underline;
  }

</style>
