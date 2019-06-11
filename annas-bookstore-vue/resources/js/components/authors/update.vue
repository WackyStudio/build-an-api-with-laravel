<template>
	<div class="container" v-if="author">
		<div class="row">
			<div class="col">
				<div class="card">
					<div class="card-header">
						Add author
					</div>
					<div class="card-body">
						<form @submit="save">
							<div class="form-group">
								<label for="name">Name</label>
								<input type="text" class="form-control" id="name" placeholder="The author's name"
									   v-model="author.name">
							</div>
							<button type="submit" class="btn btn-success float-right" :disabled="!validated">Save
								author
							</button>
							<router-link class="btn btn-link float-right" :to="{name:'authors.index'}">Cancel
							</router-link>
						</form>
					</div>

				</div>
			</div>
		</div>
	</div>
</template>

<script>
    export default {
        name: "create",
        data() {
            return {
                author:null,
            }
        },
        computed: {
            validated() {
                return String(this.author.name).length > 0;
            }
        },
        methods: {
            getAuthor(){
				jsonApi.find('author', this.$route.params.id).then(author => {
				    this.author = author.data;
				});
			},
            save() {
                if(!this.validated){
                    return;
				}

                jsonApi.update('author', {
                    id: this.author.id,
					name: this.author.name,
				}).then(response => {
                    this.$router.replace({
                        name:'authors.index',
                    });
                });
            }
        },
		created() {
            this.getAuthor();
        }
    }
</script>

<style scoped>

</style>