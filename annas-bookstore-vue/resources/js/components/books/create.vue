<template>
	<div class="container" v-if="availableAuthors">
		<div class="row">
			<div class="col">
				<div class="card">
					<div class="card-header">
						Add book
					</div>
					<div class="card-body">
						<form @submit="save">
							<div class="form-group">
								<label for="title">Title</label>
								<input type="text" class="form-control" id="title" placeholder="The book's title" v-model="title">
							</div>

							<div class="form-group">
								<label for="description">Description</label>
								<textarea class="form-control" id="description" placeholder="The book's description" v-model="description" rows="4"></textarea>
							</div>

							<div class="form-group">
								<label for="publication_year">Publication Year</label>
								<input type="text" class="form-control" id="publication_year" placeholder="The book's publication year" v-model="publication_year">
							</div>

							<div class="form-group row">
								<div class="col-6">
									<label>Authors available</label>

									<ul class="list-group">
										<draggable v-model="availableAuthors" group="authors">
											<li class="list-group-item" v-for="author in availableAuthors">{{author.name}}</li>
										</draggable>
									</ul>

								</div>

								<div class="col-6">
									<label>Authors selected</label>

									<ul class="list-group">
										<draggable v-model="authors" group="authors">
											<li class="list-group-item" v-for="author in authors">{{author.name}}</li>
										</draggable>
									</ul>

								</div>
							</div>


							<button type="submit" class="btn btn-success float-right" :disabled="!validated">Save
								book
							</button>
							<router-link class="btn btn-link float-right" :to="{name:'books.index'}">Cancel
							</router-link>
						</form>
					</div>

				</div>
			</div>
		</div>
	</div>
</template>

<script>
    import draggable from 'vuedraggable'
    export default {
        name: "update",
        components: {
            draggable,
        },
		data(){
            return {
				title:'',
				description:'',
				publication_year:'',
				authors:[],
				availableAuthors:[],
			}
		},
		computed:{
            validated(){
                return String(this.title).length > 0 &&
					String(this.description).length > 0 &&
					String(this.publication_year).length > 0 &&
					this.authors.length > 0;
			}
		},
		methods:{
            getAuthors(){
                jsonApi.findAll('author').then(authors => {
                    this.availableAuthors = authors.data;
                });
            },
            save(){
				if(!this.validated){
				    return;
				}

				jsonApi.create('book', {
                    title: this.title,
                    description: this.description,
                    publication_year: this.publication_year,
					authors: this.authors.map(item => {
                        return {
                            id: item.id,
                        }
                    }),
                }).then(response => {
                    this.$router.replace({
                        name:'books.index',
                    });
				});
			}
		},
		created() {
            this.getAuthors();
        }
    }
</script>

<style scoped>

</style>