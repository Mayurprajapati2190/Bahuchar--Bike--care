-keepattributes *Annotation*
-keep class com.bahuchar.bikecare.core.data.model.** { *; }
-keepclassmembers class * {
    @com.google.gson.annotations.SerializedName <fields>;
}
