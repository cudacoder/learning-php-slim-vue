<template>
    <div class="field is-horizontal">
        <div class="field-label">
            <label class="label" v-if="label">{{label}}</label>
        </div>
        <div class="field-body">
            <div :class="'field' + (addons ? ' has-addons' : '') + (grouped ? ' is-grouped' : '')">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "FormField",
        props: ["label", "addons", "grouped"]
    }
</script>

<style scoped>

</style>