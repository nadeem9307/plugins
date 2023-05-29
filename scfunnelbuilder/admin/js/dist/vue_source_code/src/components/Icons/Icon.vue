<script setup>
const props = defineProps({
    name: String
})
</script>
<template>
    <img :src="name" class="icon_default" alt="icon" />
</template>
<style lang="scss">
.icon_default {
    width: 16px;
    height: 16px;
    object-fit: contain;
}
</style>