export JAVA_HOME=/media/araneta/49909430-d2bd-4bcf-be1d-3c425a4013bf/apps/jdk-17.0.11
export PATH=$JAVA_HOME/bin:$PATH

export ANDROID=/media/araneta/49909430-d2bd-4bcf-be1d-3c425a4013bf/apps/Android/Sdk
export PATH=$ANDROID/tools:$PATH
export PATH=$ANDROID/tools/bin:$PATH
export PATH=$ANDROID/platform-tools:$PATH
# Android SDK
export ANDROID_SDK=/media/araneta/49909430-d2bd-4bcf-be1d-3c425a4013bf/apps/Android/Sdk
export ANDROID_SDK_ROOT=/media/araneta/49909430-d2bd-4bcf-be1d-3c425a4013bf/apps/Android/Sdk
export PATH=$ANDROID_SDK:$PATH
export ANDROID_SDK_ROOT=/media/araneta/49909430-d2bd-4bcf-be1d-3c425a4013bf/apps/Android/Sdk
# Flutter
export FLUTTER=/media/araneta/49909430-d2bd-4bcf-be1d-3c425a4013bf/apps/flutter-3.7.12/flutter
export PATH=$FLUTTER/bin:$PATH
flutter config --android-sdk ANDROID_SDK

flutter build apk

