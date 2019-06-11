<template>
	<div class="container">
		<div class="row">
			<div class="col">
				<div class="card">
					<div class="card-header">
						Authors
					</div>
					<div class="card-body">
						<div class="row mb-4">
							<div class="col">
								<router-link class="btn btn-success float-right" :to="{name:'authors.create'}">Add author</router-link>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<table class="table">
									<thead>
									<tr>
										<th>Name</th>
										<th>Number of books</th>
										<th></th>
									</tr>
									</thead>
									<tbody>
									<tr v-for="author in authors">
										<td><router-link :to="{name: 'authors.update', params:{id:author.id}}">{{author.name}}</router-link></td>
										<td>{{author.books.length}}</td>
										<td><button type="button" class="btn btn-danger btn-sm float-right" data-toggle="modal" data-target="#deleteModal" @click="setToDelete(author)">Delete</button></td>
									</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal -->
		<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="deleteModalLabel">Delete author</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" v-if="authorToDelete">
						Are you sure you want to delete {{authorToDelete.name}}?
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-link" data-dismiss="modal">No way!</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal" @click="deleteAuthor">Yes please</button>
					</div>
				</div>
			</div>
		</div>

	</div>
</template>

<script>
    export default {
        name: "index",
        data() {
            return {
                authors: [],
				authorToDelete:null,
            }
        },
		methods:{
            getAuthors(){
                jsonApi.findAll('author', {include: ['books']}).then(authors => {
                    this.authors = authors.data;
                });
			},
            setToDelete(author){
                this.authorToDelete = author;
			},
			deleteAuthor(){
				jsonApi.destroy('author', this.authorToDelete.id).then(response => {
				    this.authorToDelete = null;
				    this.getAuthors();
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