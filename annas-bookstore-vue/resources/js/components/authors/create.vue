<template>
	<div class="container">
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
									   v-model="name">
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
                name: '',
            }
        },
        computed: {
            validated() {
                return String(this.name).length > 0;
            }
        },
        methods: {
            save() {
                if(!this.validated){
                    return;
				}

                jsonApi.create('author', {
                    name: this.name
				}).then(response => {
				    this.$router.replace({
						name:'authors.index',
					});
				});
            }
        }
    }
</script>

<style scoped>

</style>