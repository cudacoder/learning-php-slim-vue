<template>
    <a @click="makeRequest" class="button">
        <slot></slot>
    </a>
</template>

<script>
    export default {
        name: "AjaxButton",
        props: ['url', 'params', 'afterDone'],
        methods: {
            makeRequest: function (e) {
                let btn = e.target.tagName.toLowerCase() === 'a' ? e.target : e.target.closest('a');
                let btnText = btn.innerHTML;
                btn.classList.add('is-loading', 'is-info');
                this.$http.post(this.url, this.params).then((res) => {
                    btn.classList.remove('is-loading', 'is-info');
                    btn.classList.add('is-success');
                    btn.innerHTML = '<span class="icon"><i class="fas fa-check"></i></span><span>Success!</span>';
                    if (this.afterDone) {
                        this.afterDone(btn, res.data);
                    }
                    setTimeout(() => {
                        btn.innerHTML = btnText;
                        btn.classList.remove('is-success');
                    }, 2000);
                }).catch((err) => {
                    console.log(err);
                    btn.classList.remove('is-loading', 'is-info');
                    btn.classList.add('is-danger');
                    btn.innerHTML = '<span class="icon"><i class="fas fa-times"></i></span><span>Error!</span>';
                    setTimeout(() => {
                        btn.innerHTML = btnText;
                        btn.classList.remove('is-danger');
                    }, 2000);
                    console.log(err)
                });
            }
        }
    }
</script>

<style scoped></style>