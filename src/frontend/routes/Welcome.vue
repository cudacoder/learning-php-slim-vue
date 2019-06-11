<template>
    <div>
        <div class="field">
            <label class="label">AWS Region:</label>
            <div class="control">
                <input v-model="aws_region" type="text" class="input">
            </div>
        </div>
        <div class="field">
            <label class="label">AWS Access Key:</label>
            <div class="control">
                <input v-model="aws_access" type="text" class="input">
            </div>
        </div>
        <div class="field">
            <label class="label">AWS Secret Key:</label>
            <div class="control">
                <input v-model="aws_secret" type="text" class="input">
            </div>
        </div>
        <div class="field">
            <div class="control">
                <button @click="initialize" class="button">Submit</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Welcome",
        data() {
            return {
                aws_secret: '',
                aws_access: '',
                aws_region: '',
            }
        },
        methods: {
            initialize: function (e) {
                let btn = document.querySelector('.button');
                btn.classList.add('is-loading');
                this.$http.post('/api/init', {
                    aws_profile: 'default',
                    aws_access: this.aws_access,
                    aws_secret: this.aws_secret,
                    aws_region: this.aws_region,
                }).then((response) => {
                    if (response.data.status) {
                        btn.classList.remove('is-loading');
                        this.$router.push('/');
                    }
                });
            }
        }
    }
</script>

<style scoped></style>