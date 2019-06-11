<template>
    <div>
        <nav class="navbar is-black" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">
                <router-link class="navbar-item" to="/">
                    <img src="/logo.png" alt="Logo">
                </router-link>
            </div>
            <div class="navbar-menu is-active">
                <div class="navbar-start">
                    <router-link class="navbar-item" v-for="navLink in params.navLinks" :to="navLink.path">
                        {{navLink.name}}
                    </router-link>
                </div>
                <div class="navbar-end">
                    <div class="navbar-item">
                            <div class="field-label">
                                <label class="label has-text-white">AWS Profile:</label>
                            </div>
                        <custom-selection class="field" v-model="profile" :options="profiles"></custom-selection>
                    </div>
                    <div class="navbar-item">
                        <div class="buttons">
                            <ajax-button url="/api/login" :params="{ profile }">
                                <span class="icon"><i class="fas fa-lock"></i></span>
                                <span>Login</span>
                            </ajax-button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <div class="section">
            <router-view/>
        </div>
    </div>
</template>

<script>
    import AjaxButton from "./components/AjaxButton.vue";
    import CustomSelection from "./components/CustomSelection.vue";

    export default {
        name: 'app',
        components: {
            CustomSelection,
            AjaxButton,
        },
        props: {
            params: Object
        },
        data() {
            return {
                profile: localStorage.getItem('profile') === null ? '' : localStorage.getItem('profile'),
                profiles: []
            }
        },
        watch: {
            profile(newProfile) {
                localStorage.setItem('profile', newProfile);
            }
        },
        created: function () {
            this.$http.get('/api/profiles').then((res) => this.profiles = res.data.items);
        },
    }
</script>

<style lang="scss"></style>
