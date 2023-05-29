<script setup>
import { ref } from 'vue'
const buttonRef = ref(null)
const props = defineProps({
    type: {
        type: String,
        default: 'button',
    },
    isDisabled: {
        type: Boolean,
        default: false,
    }
})
const emit = defineEmits(['onClick']);
defineExpose({ buttonRef })
</script>

<template>
    <button ref="buttonRef" class="btn base_btn" :type="type" @click="$emit('onClick', $event)" :disabled="isDisabled">
        <slot></slot>
    </button>
</template>
<style lang="scss"></style>
