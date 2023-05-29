<script setup>
import { onMounted, ref } from 'vue';


const props = defineProps({
    title: String,
    tabId: String,
    isActive: {
        type: Boolean,
        default: false
    },
    tabOptions: {
        required: true,
        type: Array
    }
})
let activeTabId = ref(0);
const toggleTab = (tabId, contentPage) => {
    if (activeTabId.value != tabId) {
        activeTabId.value = tabId;
        emit('getActiveTabId', tabId, contentPage);
    }
}
onMounted(() => {
    emit('getActiveTabId', activeTabId.value);
})
const emit = defineEmits(['getActiveTabId'])

</script>

<template>
    <a class="tab_c" :key="index" v-for="(tab, index) in tabOptions" :title="tab.name"
        :class="{ 'active_tab': activeTabId === index }" @click="toggleTab(index, tab.name)">
        {{ tab.name }}
    </a>
    <!-- <a class="tab_c" :key="id" v-for="{ id, name } in tabOptions" :class="{ 'active_tab': activeTabId === id }"
        @click="toggleTab(id)">
        {{ name }}
    </a> -->
</template>


<style lang="scss"></style>