<template>
    <div class="columns">
        <div class="column is-7 is-offset-2">
            <form-field label="Task Name">
                <div class="control">
                    <input class="input" type="text" v-model="name">
                </div>
            </form-field>
            <form-field label="Image">
                <div class="control">
                    <input class="input" type="text" v-model="image">
                </div>
            </form-field>
            <form-field label="Cluster">
                <div class="control">
                    <input class="input" type="text" v-model="cluster">
                </div>
            </form-field>
            <form-field label="Environment Variables" v-if="env.length !== 0">
                <div class="control">
                    <table class="table is-fullwidth">
                        <thead>
                        <tr>
                            <td><strong>Key</strong></td>
                            <td><strong>Value</strong></td>
                            <td></td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row, index) in env">
                            <td><input class="input" type="text" v-model="row.name"/></td>
                            <td><input class="input" type="text" v-model="row.value"></td>
                            <td><a @click="removeElement(index)" class="delete"></a></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </form-field>
            <form-field :label="false">
                <div class="control">
                    <button @click="addRow" class="button is-outlined is-fullwidth">Add</button>
                </div>
            </form-field>
            <br><br>
            <form-field :label="false">
                <div class="control">
                    <ajax-button class="is-success is-outlined is-fullwidth" url="/api/tasks/add"
                            :params="{ name, image, cluster, env }">
                        <span>Submit</span>
                    </ajax-button>
                </div>
            </form-field>
        </div>
    </div>
</template>

<script>

    import FormField from "../components/FormField.vue";
    import AjaxButton from "../components/AjaxButton.vue";

    export default {
        name: "Tasks",
        components: {FormField, AjaxButton},
        data() {
            return {
                name: '',
                image: '',
                cluster: '',
                env: [
                    {name: '', value: ''},
                    {name: '', value: ''},
                ]
            }
        },
        computed: {},
        methods: {
            addRow: function () {
                this.env.push({name: '', value: ''});
            },
            removeElement: function (index) {
                this.env.splice(index, 1);
            },
        }
    }
</script>

<style scoped></style>