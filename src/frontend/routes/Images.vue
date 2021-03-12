<template>
    <div class="columns">
        <div class="column is-7 is-offset-2">
            <h4 class="title is-4">Create New ECR Repository</h4>
            <form-field label="Name">
                <div class="control">
                    <!--suppress HtmlFormInputWithoutLabel -->
                    <input v-model="repoName" type="text" class="input">
                </div>
            </form-field>
            <form-field :label="false">
                <div class="control">
                    <ajax-button class="is-primary" url="/api/images/create" :params="{ profile, repoName }">
                        <span class="icon"><i class="fas fa-wrench"></i></span>
                        <span>Create</span>
                    </ajax-button>
                </div>
            </form-field>
            <h4 class="title is-4">Build New Image</h4>
            <form-field label="Project Template">
                <div class="control">
                    <div class="select">
                        <custom-selection v-model="project" :options="projects"></custom-selection>
                    </div>
                </div>
            </form-field>
            <form-field label="Image Name">
                <div class="control">
                    <!--suppress HtmlFormInputWithoutLabel -->
                    <input class="input" type="text" v-model="image">
                </div>
            </form-field>
            <form-field label="Tag">
                <div :class="'control' + (loadingTag ? ' is-loading' : '')">
                    <!--suppress HtmlFormInputWithoutLabel -->
                    <input class="input" type="text" :placeholder="tagPlaceholder"
                            v-model="tag" :disabled="loadingTag || (!!imageVersion && !this.customTagFlag)">
                </div>
            </form-field>
            <form-field label="Debug">
                <div class="control">
                    <label class="checkbox">
                        <input type="checkbox" @change="debug">
                    </label>
                </div>
            </form-field>
            <form-field :label="false">
                <div class="control">
                    <ajax-button class="is-primary" url="/api/images/build"
                            :params="{ profile, project, image, tag}">
                        <span class="icon"><i class="fas fa-wrench"></i></span>
                        <span>Build</span>
                    </ajax-button>
                    <button v-if="!!imageVersion" @click="toggle" class="button is-link">Custom Tag</button>
                </div>
            </form-field>
        </div>
    </div>
</template>

<script>
    import FormField from "../components/FormField.vue";
    import AjaxButton from "../components/AjaxButton.vue";
    import CustomSelection from "../components/CustomSelection.vue";

    export default {
        name: "Images",
        components: {FormField, CustomSelection, AjaxButton},
        methods: {
            debug: function (e) {
                this.tag = e.target.checked ? 'debug' : '';
            },
            toggle: function (e) {
                this.customTagFlag = !this.customTagFlag;
                e.target.innerText = 'Use Image Version';
                if (!this.customTagFlag) {
                    this.tag = this.imageVersion;
                    e.target.innerText = 'Custom Tag';
                }
            }
        },
        data() {
            return {
                tag: '',
                image: '',
                images: [],
                project: '',
                projects: [],
                repoName: '',
                imageVersion: '',
                loadingTag: false,
                customTagFlag: true,
                tagPlaceholder: 'A tag for the image',
                profile: localStorage.getItem('profile'),
            }
        },
        watch: {
            image(newImage) {
                this.loadingTag = true;
                this.$http.post('/api/images/version', {
                    image: newImage,
                    profile: this.profile
                }).then((res) => {
                    this.loadingTag = false;
                    let version = res.data.version;
                    if (version) {
                        this.customTagFlag = false;
                        this.imageVersion = version;
                        this.tag = this.imageVersion;
                    } else {
                        this.tagPlaceholder = 'No existent version was found, use a custom tag'
                    }
                });
            }
        },
        created: function () {
            this.$http.get('/api/projects').then((response) => {
                this.projects = response.data.items;
            });
        },
    }
</script>

<style scoped>
    .title {
        margin: 0 0 5% 0;
    }
</style>
