<template>
	<div class="container" v-if="availableAuthors && book">
		<div class="row">
			<div class="col">
				<div class="card">
					<div class="card-header">
						Update book
					</div>
					<div class="card-body">
						<form @submit="save">
							<div class="form-group">
								<label for="title">Title</label>
								<input type="text" class="form-control" id="title" placeholder="The book's title" v-model="book.title">
							</div>

							<div class="form-group">
								<label for="description">Description</label>
								<textarea class="form-control" id="description" placeholder="The book's description" v-model="book.description" rows="4"></textarea>
							</div>

							<div class="form-group">
								<label for="publication_year">Publication Year</label>
								<input type="text" class="form-control" id="publication_year" placeholder="The book's publication year" v-model="book.publication_year">
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
										<draggable v-model="book.authors" group="authors">
											<li class="list-group-item" v-for="author in book.authors">{{author.name}}</li>
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
                book:null,
				availableAuthors:[],
			}
		},
		computed:{
            validated(){
                return String(this.book.title).length > 0 &&
					String(this.book.description).length > 0 &&
					String(this.book.publication_year).length > 0 &&
					this.book.authors.length > 0;
			}
		},
		methods:{
            getBook(){
                jsonApi.find('book', this.$route.params.id, {include:['authors']}).then(book => {
                    this.book = book.data;
                    this.getAuthors();
                });
			},
            getAuthors(){
                jsonApi.findAll('author').then(authors => {
                    this.availableAuthors = _.differenceWith(authors.data, this.book.authors, (author, otherAuthor) => {
                        return author.id === otherAuthor.id
					});
                });
            },
            save(){
				if(!this.validated){
				    return;
				}

				jsonApi.update('book', {
                    id: this.book.id,
                    title: this.book.title,
                    description: this.book.description,
                    publication_year: this.book.publication_year,
					authors: this.book.authors.map(item => {
                        return {
                            id: item.id,
                        }
                    })
                }).then(response => {
                    this.$router.replace({
                        name:'books.index',
                    });
				});
			}
		},
		created() {
            this.getBook();
        }
    }
</script>

<style scoped>

</style>