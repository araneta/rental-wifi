export JAVA_HOME=/mnt/projects1/apps/jdk-17.0.11
export PATH=$JAVA_HOME/bin:$PATH

export ANDROID=/mnt/projects1/apps/Android/Sdk
export PATH=$ANDROID/tools:$PATH
export PATH=$ANDROID/tools/bin:$PATH
export PATH=$ANDROID/platform-tools:$PATH
# Android SDK
export ANDROID_SDK=/mnt/projects1/apps/Android/Sdk
export ANDROID_SDK_ROOT=/mnt/projects1/apps/Android/Sdk
export PATH=$ANDROID_SDK:$PATH
export ANDROID_SDK_ROOT=/mnt/projects1/apps/Android/Sdk
# Flutter
export FLUTTER=/mnt/projects1/apps/flutter-3.7.12/flutter
export PATH=$FLUTTER/bin:$PATH
flutter config --android-sdk ANDROID_SDK

flutter devices
#flutter build apk
flutter run -d linux
