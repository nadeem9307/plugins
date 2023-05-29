<script setup>
const props = defineProps({
    activeContent: {
        type: Boolean,
        required: true
    },
})
</script>

<template>
    <div class="tab_content_wrapper" v-if="activeContent">
        <slot></slot>
    </div>
</template>