<template>
	<div class="container">
		<div class="row">
			<div class="col">
				<passport-clients></passport-clients>
			</div>
		</div>
		<div class="row mt-4">
			<div class="col">
				<passport-personal-access-tokens></passport-personal-access-tokens>
			</div>
		</div>
		<div class="row mt-4">
			<div class="col">
				<passport-authorized-clients></passport-authorized-clients>
			</div>
		</div>
	</div>
</template>

<script>
    export default {
        name: "passport"
    }
</script>

<style scoped>

</style>