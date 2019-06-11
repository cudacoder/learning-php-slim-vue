<template>
    <div class="columns">
        <div class="column is-7 is-offset-2">
            <form-field label="Project Template">
                <div class="control">
                    <div class="select">
                        <custom-selection v-model="project" :options="projects"></custom-selection>
                    </div>
                </div>
            </form-field>
            <form-field label="Definition">
                <div class="control">
                    <div class="select">
                        <custom-selection v-model="definition" :options="definitions"></custom-selection>
                    </div>
                </div>
            </form-field>
            <form-field label="Fargate">
                <div class="control">
                    <label class="checkbox">
                        <input type="checkbox" v-model="fargate">
                    </label>
                </div>
            </form-field>
            <form-field :label="false">
                <div class="control">
                    <ajax-button class="is-primary" url="/api/docker/deploy"
                            :params="{ profile, project, definition, fargate, vault }">
                        <span class="icon"><i class="fas fa-rocket"></i></span>
                        <span>Deploy</span>
                    </ajax-button>
                </div>
            </form-field>
        </div>
    </div>
</template>

<script>
    import AjaxButton from "../components/AjaxButton.vue";
    import CustomSelection from "../components/CustomSelection.vue";
    import FormField from "../components/FormField.vue";

    export default {
        name: "Deploy",
        components: {FormField, CustomSelection, AjaxButton},
        data() {
            return {
                project: '',
                projects: [],
                fargate: false,
                definition: '',
                definitions: [],
                profile: localStorage.getItem('profile'),
            }
        },
        created: function () {
            let vm = this;
            let reqArray = [
                this.$http.get('/api/projects'),
                this.$http.get('/api/tasks')
            ];
            this.$http.all(reqArray).then(
                this.$http.spread(
                    function (projects, definitions) {
                        vm.projects = projects.data.items;
                        vm.definitions = definitions.data.items;
                    }
                )
            );
        },
    }
</script>

<style scoped></style>