<script setup>
import hand from "@/assets/icons/hand.svg";
import filter from "@/assets/icons/filter.svg";
import eye from "@/assets/icons/eye.svg";
import Icon from "../Icons/Icon.vue";
const props = defineProps({
    conversion_rate_value: Number,
    visit_value: Number,
    conversion_value: Number
})
</script>
<template>
    <div class="card_stats">
        <div class="icon_stats">
            <Icon :name="eye" class="icon_tt" />
            <span>{{ visit_value }}</span>
        </div>
        <div class="icon_stats">
            <Icon :name="hand" class="icon_tt" />
            <span>{{ conversion_value }}</span>
        </div>
        <div class="icon_stats">
            <Icon :name="filter" class="icon_tt" />
            <span>{{ conversion_rate_value }}</span>
        </div>
    </div>
</template>