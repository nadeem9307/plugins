<script setup>
import { ref } from 'vue';
import FunnelLayout from "./layouts/FunnelLayout.vue";
import Loader from './components/loader/Loader.vue';
import { getFunnelDetails } from "@/api/funnel/funnel-service";
const props = defineProps({
  funnelId: String
});

let loadingStatus = ref(null);

const getAPIData = async () => {
  loadingStatus.value = true;
  const {
    loading,
    error,
    response,
  } = await getFunnelDetails(props.funnelId);
  loadingStatus.value = loading;
}
getAPIData();
</script>

<template>
  <FunnelLayout v-if="!loadingStatus" />
  <Loader v-else class="app_loader" />
</template>
<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=PT+Serif:wght@400;700&display=swap');
</style>
