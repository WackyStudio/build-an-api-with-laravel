<template>
	<div class="container">
		<div class="row">
			<div class="col">
				<div class="card">
					<div class="card-header">
						Books
					</div>
					<div class="card-body">
						<div class="row mb-4">
							<div class="col">
								<router-link class="btn btn-success float-right" :to="{name:'books.create'}">Add book</router-link>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<table class="table">
									<thead>
									<tr>
										<th>Title</th>
										<th>Number of authors</th>
										<th></th>
									</tr>
									</thead>
									<tbody>
									<tr v-for="book in books">
										<td><router-link :to="{name:'books.update', params:{id:book.id}}">{{book.title}}</router-link></td>
										<td>{{book.authors.length}}</td>
										<td><button type="button" class="btn btn-danger btn-sm float-right" data-toggle="modal" data-target="#deleteModal" @click="setToDelete(book)">Delete</button></td>
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
						<h5 class="modal-title" id="deleteModalLabel">Delete book</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" v-if="bookToDelete">
						Are you sure you want to delete {{bookToDelete.title}}?
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-link" data-dismiss="modal">No way!</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal" @click="deleteBook">Yes please</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
    export default {
        name: "index",
		data(){
          return {
              books:[],
			  bookToDelete:null,
		  }
		},
		methods:{
            getBooks(){
                jsonApi.findAll('book', {include: ['authors']}).then(books => {
                    this.books = books.data;
                })
			},
            setToDelete(book){
                this.bookToDelete = book;
			},
			deleteBook(){
                jsonApi.destroy('book', this.bookToDelete.id).then(response => {
                    this.bookToDelete = null;
                    this.getBooks();
                });
			}
		},
        created() {
			this.getBooks();
        }
    }
</script>

<style scoped>

</style>