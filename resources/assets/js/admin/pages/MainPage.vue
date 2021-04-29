<template>
	<div class="main-page-settings">
		<div class="setting main-page-warning">
			<div class="title">Предупреждение на главной странице</div>
			<div class="main-page-warnings-container">
				<v-text-field :hide-details="true"  dense  class="plus-input"
				              placeholder="Заголовок"
				              v-model="mainPageWarning.title"></v-text-field>
				<v-text-field :hide-details="true"  dense  class="plus-input"
				              placeholder="Текст"
				              v-model="mainPageWarning.description"></v-text-field>
				<v-checkbox label="Включено" v-model="mainPageWarning.visible"></v-checkbox>
			</div>
			<span class="control fa fa-save mr-5" v-on:click="saveMainPageWarning()"></span>
		</div>

		<div class="settings pluses">
			<div class="title">Преимущества</div>
			<div class="pluses-container">
				<div class="plus" v-for="(plus, index) in pluses">
					<v-text-field :hide-details="true"  dense  :key="index + 'phone'"
					              @click:append-outer="removePlus(plus)"
					              append-outer-icon="fa fa-times"
					              class="plus-input"
					              v-model="plus.plus"></v-text-field>
				</div>
			</div>

			<span class="control fa fa-save mr-5" v-on:click="savePluses()"></span>
			<span class="control fa fa-plus" v-on:click="addPlus()"></span>
		</div>


		<div class="settings slides">
			<div class="title">Фоновые изображения</div>
			<div class="slides-container">
				<div class="slides-desktop">
					Десктоп:

					<div class="slide" v-for="(slide, index) in desktopSlides">
						<img :src="slide.slide_path"
						     width="170"
						     height="100"
						     alt="">
						<div class="controls-and-name">
							<div class="name">{{slide.name}}</div>
							<div class="controls">
								<span class="control" v-on:click="remove(slide, 'desktop')">
									<i class="fa fa-times"></i>
								</span>
								<span class="control"
								      v-show="index > 0"
								      v-on:click="move(slide, 'desktop', index, index - 1)">
									<i class="fa fa-arrow-up"></i>
								</span>

								<span class="control"
								      v-show="index < desktopSlides.length - 1"
								      v-on:click="move(slide, 'desktop', index, index + 1)">
									<i class="fa fa-arrow-down"></i>
								</span>
							</div>
						</div>
					</div>
					<input type="file" id="addFileDesktop" class="addFile" accept="image/jpeg,image/png" v-on:change="upload($event, 'desktop')" />
				</div>

				<div class="slides-mobile">
					Мобильные:
					<div class="slide" v-for="(slide, index) in mobileSlides">
						<img :src="slide.slide_path"
						     width="66"
						     height="100"
						     alt="">
						<div class="controls-and-name">
							<div class="name">{{slide.name}}</div>
							<div class="controls">
								<span class="control" v-on:click="remove(slide, 'mobile')">
									<i class="fa fa-times"></i>
								</span>
								<span class="control"
								      v-show="index > 0"
								      v-on:click="move(slide, 'mobile', index, index - 1)">
									<i class="fa fa-arrow-up"></i>
								</span>

								<span class="control"
								      v-show="index < mobileSlides.length"
								      v-on:click="move(slide, 'mobile', index, index + 1)">
									<i class="fa fa-arrow-down"></i>
								</span>
							</div>
						</div>
					</div>
					<input type="file" id="addFileMobile" class="addFile" accept="image/jpeg,image/png" v-on:change="upload($event, 'mobile')" />
				</div>
			</div>
		</div>


	</div>
</template>

<script>
	export default {
		name: "MainPage",
		data: function(){
			return {
				mainPageWarning: {},
				desktopSlides: [],
				mobileSlides: [],
				pluses: [],
			}
		},

		mounted(){
			this.getSlides('desktop');
			this.getSlides('mobile');
			this.getPluses();
			this.getMainPageWarning();
		},
		methods: {
			getMainPageWarning(){
				axios.get('/admin/warning/get').then((response) => {
					this.mainPageWarning = response.data;
				})
			},
			saveMainPageWarning(){
				axios.post('/admin/warning/save', this.mainPageWarning).then((response) => {
					this.mainPageWarning = response.data;
				})
			},
			getPluses(){
				axios.get('/admin/pluses/list').then((response) => {
					this.pluses = response.data;
				})
			},
			removePlus(plus){
				if(plus.id && !window.confirm('Вы уверены?')){
					return;
				}

				axios.post('/admin/pluses/delete', {id: plus.id}).then(() => {
					this.getPluses()
				})
			},
			addPlus(){
				this.pluses.push({
					'plus': '',
					'position': 100
				})
			},
			movePlus(plus, type, index, newIndex){
				let pairedPlus;

				pairedPlus = this.pluses[newIndex];

				axios.post('/admin/pluses/move', {
					id: slide.id,
					position: newIndex,
					paired_id: pairedPlus.id,
					paired_position: index
				}).then(() => {
					this.getPluses()
				})
			},
			savePluses(){
				axios.post('/admin/pluses/save', {
					pluses: this.pluses
				}).then(() => {
					this.getPluses()
				})
			},

			getSlides(type){
				axios.post('/admin/slides/list', {type: type}).then((response) => {
					this[type + 'Slides'] = response.data;
				})
			},

			remove(slide, type){
				if(!window.confirm('Вы уверены?')){
					return;
				}

				axios.post('/admin/slides/delete', {id: slide.id}).then(() => {
					this.getSlides(type)
				})
			},

			move(slide, type, index, newIndex){
				let pairedSlide;

				pairedSlide = this[type + 'Slides'][newIndex];

				axios.post('/admin/slides/move', {
					id: slide.id,
					position: newIndex,
					paired_id: pairedSlide.id,
					paired_position: index
				}).then(() => {
					this.getSlides(type)
				})
			},

			upload(event, type){
				let file = event.target.files[0];

				let formData = new FormData();
				formData.append('file', file);
				formData.append('type', type);

				axios.post('/admin/slides/upload', formData, {
					headers: {
						'Content-Type': 'multipart/form-data'
					}
				}).then(() => {
					this.getSlides(type);
					$('#addFileDesktop')[0].file = null;
					$('#addFileMobile')[0].file = null;
				})
			}
		}
	}
</script>

<style scoped>

</style>
