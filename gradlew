#!/bin/sh
#
# Gradle wrapper bootstrap script
#

GRADLE_WRAPPER_JAR="gradle/wrapper/gradle-wrapper.jar"

# تأكد من أن ملف gradlew لديه صلاحية التنفيذ
if [ ! -x "$0" ]; then
    echo "Setting execution permission for gradlew..."
    chmod +x "$0"
fi

# تحقق من وجود ملف الـ wrapper JAR، وإذا لم يكن موجودًا، أنشئه
if [ ! -f "$GRADLE_WRAPPER_JAR" ]; then
    echo "Gradle wrapper JAR not found! Running Gradle to generate it..."
    ./gradlew wrapper --gradle-version 7.5
fi

# تشغيل Gradle
exec java -jar "$GRADLE_WRAPPER_JAR" "$@"
