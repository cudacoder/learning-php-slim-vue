<template>
    <div class="select">
        <!--suppress JSUnresolvedVariable -->
        <select :value="value" @change="$emit('input', $event.target.value)" title="">
            <option disabled value="" selected>Select a Value</option>
            <option v-for="op in options"
                    :value="op.hasOwnProperty('id') ? op.id : op"
                    :selected="op.hasOwnProperty('id') ? (op.id === value) : (value === op)">
                {{op.hasOwnProperty('name') ? op.name : op}}
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        name: "CustomSelection",
        props: ['value', 'options'],
    }
</script>

<style scoped>

</style>