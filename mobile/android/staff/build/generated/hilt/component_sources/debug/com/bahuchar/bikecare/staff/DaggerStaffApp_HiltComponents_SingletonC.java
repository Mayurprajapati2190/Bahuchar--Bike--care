package com.bahuchar.bikecare.staff;

import android.app.Activity;
import android.app.Service;
import android.view.View;
import androidx.fragment.app.Fragment;
import androidx.lifecycle.SavedStateHandle;
import androidx.lifecycle.ViewModel;
import com.bahuchar.bikecare.core.data.api.StaffApi;
import com.bahuchar.bikecare.core.data.local.TokenStore;
import com.bahuchar.bikecare.core.data.network.ApiClient;
import com.bahuchar.bikecare.core.data.network.AuthInterceptor;
import com.bahuchar.bikecare.core.data.network.NetworkConfig;
import com.bahuchar.bikecare.core.data.network.UnauthorizedInterceptor;
import com.bahuchar.bikecare.staff.data.StaffRepository;
import com.bahuchar.bikecare.staff.di.StaffNetworkModule_ProvideNetworkConfigFactory;
import com.bahuchar.bikecare.staff.di.StaffNetworkModule_ProvideStaffApiFactory;
import com.bahuchar.bikecare.staff.ui.auth.AuthViewModel;
import com.bahuchar.bikecare.staff.ui.auth.AuthViewModel_HiltModules;
import com.bahuchar.bikecare.staff.ui.bills.BillDetailViewModel;
import com.bahuchar.bikecare.staff.ui.bills.BillDetailViewModel_HiltModules;
import com.bahuchar.bikecare.staff.ui.bills.BillsViewModel;
import com.bahuchar.bikecare.staff.ui.bills.BillsViewModel_HiltModules;
import com.bahuchar.bikecare.staff.ui.customers.CreateCustomerViewModel;
import com.bahuchar.bikecare.staff.ui.customers.CreateCustomerViewModel_HiltModules;
import com.bahuchar.bikecare.staff.ui.customers.CustomerDetailViewModel;
import com.bahuchar.bikecare.staff.ui.customers.CustomerDetailViewModel_HiltModules;
import com.bahuchar.bikecare.staff.ui.customers.CustomersViewModel;
import com.bahuchar.bikecare.staff.ui.customers.CustomersViewModel_HiltModules;
import com.bahuchar.bikecare.staff.ui.dashboard.DashboardViewModel;
import com.bahuchar.bikecare.staff.ui.dashboard.DashboardViewModel_HiltModules;
import com.bahuchar.bikecare.staff.ui.services.CreateServiceViewModel;
import com.bahuchar.bikecare.staff.ui.services.CreateServiceViewModel_HiltModules;
import com.bahuchar.bikecare.staff.ui.services.ServiceDetailViewModel;
import com.bahuchar.bikecare.staff.ui.services.ServiceDetailViewModel_HiltModules;
import com.bahuchar.bikecare.staff.ui.services.ServicesViewModel;
import com.bahuchar.bikecare.staff.ui.services.ServicesViewModel_HiltModules;
import dagger.hilt.android.ActivityRetainedLifecycle;
import dagger.hilt.android.ViewModelLifecycle;
import dagger.hilt.android.internal.builders.ActivityComponentBuilder;
import dagger.hilt.android.internal.builders.ActivityRetainedComponentBuilder;
import dagger.hilt.android.internal.builders.FragmentComponentBuilder;
import dagger.hilt.android.internal.builders.ServiceComponentBuilder;
import dagger.hilt.android.internal.builders.ViewComponentBuilder;
import dagger.hilt.android.internal.builders.ViewModelComponentBuilder;
import dagger.hilt.android.internal.builders.ViewWithFragmentComponentBuilder;
import dagger.hilt.android.internal.lifecycle.DefaultViewModelFactories;
import dagger.hilt.android.internal.lifecycle.DefaultViewModelFactories_InternalFactoryFactory_Factory;
import dagger.hilt.android.internal.managers.ActivityRetainedComponentManager_LifecycleModule_ProvideActivityRetainedLifecycleFactory;
import dagger.hilt.android.internal.managers.SavedStateHandleHolder;
import dagger.hilt.android.internal.modules.ApplicationContextModule;
import dagger.hilt.android.internal.modules.ApplicationContextModule_ProvideContextFactory;
import dagger.internal.DaggerGenerated;
import dagger.internal.DoubleCheck;
import dagger.internal.IdentifierNameString;
import dagger.internal.KeepFieldType;
import dagger.internal.LazyClassKeyMap;
import dagger.internal.MapBuilder;
import dagger.internal.Preconditions;
import dagger.internal.Provider;
import java.util.Collections;
import java.util.Map;
import java.util.Set;
import javax.annotation.processing.Generated;

@DaggerGenerated
@Generated(
    value = "dagger.internal.codegen.ComponentProcessor",
    comments = "https://dagger.dev"
)
@SuppressWarnings({
    "unchecked",
    "rawtypes",
    "KotlinInternal",
    "KotlinInternalInJava",
    "cast",
    "deprecation"
})
public final class DaggerStaffApp_HiltComponents_SingletonC {
  private DaggerStaffApp_HiltComponents_SingletonC() {
  }

  public static Builder builder() {
    return new Builder();
  }

  public static final class Builder {
    private ApplicationContextModule applicationContextModule;

    private Builder() {
    }

    public Builder applicationContextModule(ApplicationContextModule applicationContextModule) {
      this.applicationContextModule = Preconditions.checkNotNull(applicationContextModule);
      return this;
    }

    public StaffApp_HiltComponents.SingletonC build() {
      Preconditions.checkBuilderRequirement(applicationContextModule, ApplicationContextModule.class);
      return new SingletonCImpl(applicationContextModule);
    }
  }

  private static final class ActivityRetainedCBuilder implements StaffApp_HiltComponents.ActivityRetainedC.Builder {
    private final SingletonCImpl singletonCImpl;

    private SavedStateHandleHolder savedStateHandleHolder;

    private ActivityRetainedCBuilder(SingletonCImpl singletonCImpl) {
      this.singletonCImpl = singletonCImpl;
    }

    @Override
    public ActivityRetainedCBuilder savedStateHandleHolder(
        SavedStateHandleHolder savedStateHandleHolder) {
      this.savedStateHandleHolder = Preconditions.checkNotNull(savedStateHandleHolder);
      return this;
    }

    @Override
    public StaffApp_HiltComponents.ActivityRetainedC build() {
      Preconditions.checkBuilderRequirement(savedStateHandleHolder, SavedStateHandleHolder.class);
      return new ActivityRetainedCImpl(singletonCImpl, savedStateHandleHolder);
    }
  }

  private static final class ActivityCBuilder implements StaffApp_HiltComponents.ActivityC.Builder {
    private final SingletonCImpl singletonCImpl;

    private final ActivityRetainedCImpl activityRetainedCImpl;

    private Activity activity;

    private ActivityCBuilder(SingletonCImpl singletonCImpl,
        ActivityRetainedCImpl activityRetainedCImpl) {
      this.singletonCImpl = singletonCImpl;
      this.activityRetainedCImpl = activityRetainedCImpl;
    }

    @Override
    public ActivityCBuilder activity(Activity activity) {
      this.activity = Preconditions.checkNotNull(activity);
      return this;
    }

    @Override
    public StaffApp_HiltComponents.ActivityC build() {
      Preconditions.checkBuilderRequirement(activity, Activity.class);
      return new ActivityCImpl(singletonCImpl, activityRetainedCImpl, activity);
    }
  }

  private static final class FragmentCBuilder implements StaffApp_HiltComponents.FragmentC.Builder {
    private final SingletonCImpl singletonCImpl;

    private final ActivityRetainedCImpl activityRetainedCImpl;

    private final ActivityCImpl activityCImpl;

    private Fragment fragment;

    private FragmentCBuilder(SingletonCImpl singletonCImpl,
        ActivityRetainedCImpl activityRetainedCImpl, ActivityCImpl activityCImpl) {
      this.singletonCImpl = singletonCImpl;
      this.activityRetainedCImpl = activityRetainedCImpl;
      this.activityCImpl = activityCImpl;
    }

    @Override
    public FragmentCBuilder fragment(Fragment fragment) {
      this.fragment = Preconditions.checkNotNull(fragment);
      return this;
    }

    @Override
    public StaffApp_HiltComponents.FragmentC build() {
      Preconditions.checkBuilderRequirement(fragment, Fragment.class);
      return new FragmentCImpl(singletonCImpl, activityRetainedCImpl, activityCImpl, fragment);
    }
  }

  private static final class ViewWithFragmentCBuilder implements StaffApp_HiltComponents.ViewWithFragmentC.Builder {
    private final SingletonCImpl singletonCImpl;

    private final ActivityRetainedCImpl activityRetainedCImpl;

    private final ActivityCImpl activityCImpl;

    private final FragmentCImpl fragmentCImpl;

    private View view;

    private ViewWithFragmentCBuilder(SingletonCImpl singletonCImpl,
        ActivityRetainedCImpl activityRetainedCImpl, ActivityCImpl activityCImpl,
        FragmentCImpl fragmentCImpl) {
      this.singletonCImpl = singletonCImpl;
      this.activityRetainedCImpl = activityRetainedCImpl;
      this.activityCImpl = activityCImpl;
      this.fragmentCImpl = fragmentCImpl;
    }

    @Override
    public ViewWithFragmentCBuilder view(View view) {
      this.view = Preconditions.checkNotNull(view);
      return this;
    }

    @Override
    public StaffApp_HiltComponents.ViewWithFragmentC build() {
      Preconditions.checkBuilderRequirement(view, View.class);
      return new ViewWithFragmentCImpl(singletonCImpl, activityRetainedCImpl, activityCImpl, fragmentCImpl, view);
    }
  }

  private static final class ViewCBuilder implements StaffApp_HiltComponents.ViewC.Builder {
    private final SingletonCImpl singletonCImpl;

    private final ActivityRetainedCImpl activityRetainedCImpl;

    private final ActivityCImpl activityCImpl;

    private View view;

    private ViewCBuilder(SingletonCImpl singletonCImpl, ActivityRetainedCImpl activityRetainedCImpl,
        ActivityCImpl activityCImpl) {
      this.singletonCImpl = singletonCImpl;
      this.activityRetainedCImpl = activityRetainedCImpl;
      this.activityCImpl = activityCImpl;
    }

    @Override
    public ViewCBuilder view(View view) {
      this.view = Preconditions.checkNotNull(view);
      return this;
    }

    @Override
    public StaffApp_HiltComponents.ViewC build() {
      Preconditions.checkBuilderRequirement(view, View.class);
      return new ViewCImpl(singletonCImpl, activityRetainedCImpl, activityCImpl, view);
    }
  }

  private static final class ViewModelCBuilder implements StaffApp_HiltComponents.ViewModelC.Builder {
    private final SingletonCImpl singletonCImpl;

    private final ActivityRetainedCImpl activityRetainedCImpl;

    private SavedStateHandle savedStateHandle;

    private ViewModelLifecycle viewModelLifecycle;

    private ViewModelCBuilder(SingletonCImpl singletonCImpl,
        ActivityRetainedCImpl activityRetainedCImpl) {
      this.singletonCImpl = singletonCImpl;
      this.activityRetainedCImpl = activityRetainedCImpl;
    }

    @Override
    public ViewModelCBuilder savedStateHandle(SavedStateHandle handle) {
      this.savedStateHandle = Preconditions.checkNotNull(handle);
      return this;
    }

    @Override
    public ViewModelCBuilder viewModelLifecycle(ViewModelLifecycle viewModelLifecycle) {
      this.viewModelLifecycle = Preconditions.checkNotNull(viewModelLifecycle);
      return this;
    }

    @Override
    public StaffApp_HiltComponents.ViewModelC build() {
      Preconditions.checkBuilderRequirement(savedStateHandle, SavedStateHandle.class);
      Preconditions.checkBuilderRequirement(viewModelLifecycle, ViewModelLifecycle.class);
      return new ViewModelCImpl(singletonCImpl, activityRetainedCImpl, savedStateHandle, viewModelLifecycle);
    }
  }

  private static final class ServiceCBuilder implements StaffApp_HiltComponents.ServiceC.Builder {
    private final SingletonCImpl singletonCImpl;

    private Service service;

    private ServiceCBuilder(SingletonCImpl singletonCImpl) {
      this.singletonCImpl = singletonCImpl;
    }

    @Override
    public ServiceCBuilder service(Service service) {
      this.service = Preconditions.checkNotNull(service);
      return this;
    }

    @Override
    public StaffApp_HiltComponents.ServiceC build() {
      Preconditions.checkBuilderRequirement(service, Service.class);
      return new ServiceCImpl(singletonCImpl, service);
    }
  }

  private static final class ViewWithFragmentCImpl extends StaffApp_HiltComponents.ViewWithFragmentC {
    private final SingletonCImpl singletonCImpl;

    private final ActivityRetainedCImpl activityRetainedCImpl;

    private final ActivityCImpl activityCImpl;

    private final FragmentCImpl fragmentCImpl;

    private final ViewWithFragmentCImpl viewWithFragmentCImpl = this;

    private ViewWithFragmentCImpl(SingletonCImpl singletonCImpl,
        ActivityRetainedCImpl activityRetainedCImpl, ActivityCImpl activityCImpl,
        FragmentCImpl fragmentCImpl, View viewParam) {
      this.singletonCImpl = singletonCImpl;
      this.activityRetainedCImpl = activityRetainedCImpl;
      this.activityCImpl = activityCImpl;
      this.fragmentCImpl = fragmentCImpl;


    }
  }

  private static final class FragmentCImpl extends StaffApp_HiltComponents.FragmentC {
    private final SingletonCImpl singletonCImpl;

    private final ActivityRetainedCImpl activityRetainedCImpl;

    private final ActivityCImpl activityCImpl;

    private final FragmentCImpl fragmentCImpl = this;

    private FragmentCImpl(SingletonCImpl singletonCImpl,
        ActivityRetainedCImpl activityRetainedCImpl, ActivityCImpl activityCImpl,
        Fragment fragmentParam) {
      this.singletonCImpl = singletonCImpl;
      this.activityRetainedCImpl = activityRetainedCImpl;
      this.activityCImpl = activityCImpl;


    }

    @Override
    public DefaultViewModelFactories.InternalFactoryFactory getHiltInternalFactoryFactory() {
      return activityCImpl.getHiltInternalFactoryFactory();
    }

    @Override
    public ViewWithFragmentComponentBuilder viewWithFragmentComponentBuilder() {
      return new ViewWithFragmentCBuilder(singletonCImpl, activityRetainedCImpl, activityCImpl, fragmentCImpl);
    }
  }

  private static final class ViewCImpl extends StaffApp_HiltComponents.ViewC {
    private final SingletonCImpl singletonCImpl;

    private final ActivityRetainedCImpl activityRetainedCImpl;

    private final ActivityCImpl activityCImpl;

    private final ViewCImpl viewCImpl = this;

    private ViewCImpl(SingletonCImpl singletonCImpl, ActivityRetainedCImpl activityRetainedCImpl,
        ActivityCImpl activityCImpl, View viewParam) {
      this.singletonCImpl = singletonCImpl;
      this.activityRetainedCImpl = activityRetainedCImpl;
      this.activityCImpl = activityCImpl;


    }
  }

  private static final class ActivityCImpl extends StaffApp_HiltComponents.ActivityC {
    private final SingletonCImpl singletonCImpl;

    private final ActivityRetainedCImpl activityRetainedCImpl;

    private final ActivityCImpl activityCImpl = this;

    private ActivityCImpl(SingletonCImpl singletonCImpl,
        ActivityRetainedCImpl activityRetainedCImpl, Activity activityParam) {
      this.singletonCImpl = singletonCImpl;
      this.activityRetainedCImpl = activityRetainedCImpl;


    }

    @Override
    public void injectMainActivity(MainActivity arg0) {
    }

    @Override
    public DefaultViewModelFactories.InternalFactoryFactory getHiltInternalFactoryFactory() {
      return DefaultViewModelFactories_InternalFactoryFactory_Factory.newInstance(getViewModelKeys(), new ViewModelCBuilder(singletonCImpl, activityRetainedCImpl));
    }

    @Override
    public Map<Class<?>, Boolean> getViewModelKeys() {
      return LazyClassKeyMap.<Boolean>of(MapBuilder.<String, Boolean>newMapBuilder(10).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_auth_AuthViewModel, AuthViewModel_HiltModules.KeyModule.provide()).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_bills_BillDetailViewModel, BillDetailViewModel_HiltModules.KeyModule.provide()).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_bills_BillsViewModel, BillsViewModel_HiltModules.KeyModule.provide()).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_customers_CreateCustomerViewModel, CreateCustomerViewModel_HiltModules.KeyModule.provide()).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_services_CreateServiceViewModel, CreateServiceViewModel_HiltModules.KeyModule.provide()).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_customers_CustomerDetailViewModel, CustomerDetailViewModel_HiltModules.KeyModule.provide()).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_customers_CustomersViewModel, CustomersViewModel_HiltModules.KeyModule.provide()).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_dashboard_DashboardViewModel, DashboardViewModel_HiltModules.KeyModule.provide()).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_services_ServiceDetailViewModel, ServiceDetailViewModel_HiltModules.KeyModule.provide()).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_services_ServicesViewModel, ServicesViewModel_HiltModules.KeyModule.provide()).build());
    }

    @Override
    public ViewModelComponentBuilder getViewModelComponentBuilder() {
      return new ViewModelCBuilder(singletonCImpl, activityRetainedCImpl);
    }

    @Override
    public FragmentComponentBuilder fragmentComponentBuilder() {
      return new FragmentCBuilder(singletonCImpl, activityRetainedCImpl, activityCImpl);
    }

    @Override
    public ViewComponentBuilder viewComponentBuilder() {
      return new ViewCBuilder(singletonCImpl, activityRetainedCImpl, activityCImpl);
    }

    @IdentifierNameString
    private static final class LazyClassKeyProvider {
      static String com_bahuchar_bikecare_staff_ui_dashboard_DashboardViewModel = "com.bahuchar.bikecare.staff.ui.dashboard.DashboardViewModel";

      static String com_bahuchar_bikecare_staff_ui_services_ServicesViewModel = "com.bahuchar.bikecare.staff.ui.services.ServicesViewModel";

      static String com_bahuchar_bikecare_staff_ui_auth_AuthViewModel = "com.bahuchar.bikecare.staff.ui.auth.AuthViewModel";

      static String com_bahuchar_bikecare_staff_ui_bills_BillsViewModel = "com.bahuchar.bikecare.staff.ui.bills.BillsViewModel";

      static String com_bahuchar_bikecare_staff_ui_customers_CreateCustomerViewModel = "com.bahuchar.bikecare.staff.ui.customers.CreateCustomerViewModel";

      static String com_bahuchar_bikecare_staff_ui_customers_CustomerDetailViewModel = "com.bahuchar.bikecare.staff.ui.customers.CustomerDetailViewModel";

      static String com_bahuchar_bikecare_staff_ui_services_ServiceDetailViewModel = "com.bahuchar.bikecare.staff.ui.services.ServiceDetailViewModel";

      static String com_bahuchar_bikecare_staff_ui_bills_BillDetailViewModel = "com.bahuchar.bikecare.staff.ui.bills.BillDetailViewModel";

      static String com_bahuchar_bikecare_staff_ui_services_CreateServiceViewModel = "com.bahuchar.bikecare.staff.ui.services.CreateServiceViewModel";

      static String com_bahuchar_bikecare_staff_ui_customers_CustomersViewModel = "com.bahuchar.bikecare.staff.ui.customers.CustomersViewModel";

      @KeepFieldType
      DashboardViewModel com_bahuchar_bikecare_staff_ui_dashboard_DashboardViewModel2;

      @KeepFieldType
      ServicesViewModel com_bahuchar_bikecare_staff_ui_services_ServicesViewModel2;

      @KeepFieldType
      AuthViewModel com_bahuchar_bikecare_staff_ui_auth_AuthViewModel2;

      @KeepFieldType
      BillsViewModel com_bahuchar_bikecare_staff_ui_bills_BillsViewModel2;

      @KeepFieldType
      CreateCustomerViewModel com_bahuchar_bikecare_staff_ui_customers_CreateCustomerViewModel2;

      @KeepFieldType
      CustomerDetailViewModel com_bahuchar_bikecare_staff_ui_customers_CustomerDetailViewModel2;

      @KeepFieldType
      ServiceDetailViewModel com_bahuchar_bikecare_staff_ui_services_ServiceDetailViewModel2;

      @KeepFieldType
      BillDetailViewModel com_bahuchar_bikecare_staff_ui_bills_BillDetailViewModel2;

      @KeepFieldType
      CreateServiceViewModel com_bahuchar_bikecare_staff_ui_services_CreateServiceViewModel2;

      @KeepFieldType
      CustomersViewModel com_bahuchar_bikecare_staff_ui_customers_CustomersViewModel2;
    }
  }

  private static final class ViewModelCImpl extends StaffApp_HiltComponents.ViewModelC {
    private final SingletonCImpl singletonCImpl;

    private final ActivityRetainedCImpl activityRetainedCImpl;

    private final ViewModelCImpl viewModelCImpl = this;

    private Provider<AuthViewModel> authViewModelProvider;

    private Provider<BillDetailViewModel> billDetailViewModelProvider;

    private Provider<BillsViewModel> billsViewModelProvider;

    private Provider<CreateCustomerViewModel> createCustomerViewModelProvider;

    private Provider<CreateServiceViewModel> createServiceViewModelProvider;

    private Provider<CustomerDetailViewModel> customerDetailViewModelProvider;

    private Provider<CustomersViewModel> customersViewModelProvider;

    private Provider<DashboardViewModel> dashboardViewModelProvider;

    private Provider<ServiceDetailViewModel> serviceDetailViewModelProvider;

    private Provider<ServicesViewModel> servicesViewModelProvider;

    private ViewModelCImpl(SingletonCImpl singletonCImpl,
        ActivityRetainedCImpl activityRetainedCImpl, SavedStateHandle savedStateHandleParam,
        ViewModelLifecycle viewModelLifecycleParam) {
      this.singletonCImpl = singletonCImpl;
      this.activityRetainedCImpl = activityRetainedCImpl;

      initialize(savedStateHandleParam, viewModelLifecycleParam);

    }

    @SuppressWarnings("unchecked")
    private void initialize(final SavedStateHandle savedStateHandleParam,
        final ViewModelLifecycle viewModelLifecycleParam) {
      this.authViewModelProvider = new SwitchingProvider<>(singletonCImpl, activityRetainedCImpl, viewModelCImpl, 0);
      this.billDetailViewModelProvider = new SwitchingProvider<>(singletonCImpl, activityRetainedCImpl, viewModelCImpl, 1);
      this.billsViewModelProvider = new SwitchingProvider<>(singletonCImpl, activityRetainedCImpl, viewModelCImpl, 2);
      this.createCustomerViewModelProvider = new SwitchingProvider<>(singletonCImpl, activityRetainedCImpl, viewModelCImpl, 3);
      this.createServiceViewModelProvider = new SwitchingProvider<>(singletonCImpl, activityRetainedCImpl, viewModelCImpl, 4);
      this.customerDetailViewModelProvider = new SwitchingProvider<>(singletonCImpl, activityRetainedCImpl, viewModelCImpl, 5);
      this.customersViewModelProvider = new SwitchingProvider<>(singletonCImpl, activityRetainedCImpl, viewModelCImpl, 6);
      this.dashboardViewModelProvider = new SwitchingProvider<>(singletonCImpl, activityRetainedCImpl, viewModelCImpl, 7);
      this.serviceDetailViewModelProvider = new SwitchingProvider<>(singletonCImpl, activityRetainedCImpl, viewModelCImpl, 8);
      this.servicesViewModelProvider = new SwitchingProvider<>(singletonCImpl, activityRetainedCImpl, viewModelCImpl, 9);
    }

    @Override
    public Map<Class<?>, javax.inject.Provider<ViewModel>> getHiltViewModelMap() {
      return LazyClassKeyMap.<javax.inject.Provider<ViewModel>>of(MapBuilder.<String, javax.inject.Provider<ViewModel>>newMapBuilder(10).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_auth_AuthViewModel, ((Provider) authViewModelProvider)).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_bills_BillDetailViewModel, ((Provider) billDetailViewModelProvider)).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_bills_BillsViewModel, ((Provider) billsViewModelProvider)).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_customers_CreateCustomerViewModel, ((Provider) createCustomerViewModelProvider)).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_services_CreateServiceViewModel, ((Provider) createServiceViewModelProvider)).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_customers_CustomerDetailViewModel, ((Provider) customerDetailViewModelProvider)).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_customers_CustomersViewModel, ((Provider) customersViewModelProvider)).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_dashboard_DashboardViewModel, ((Provider) dashboardViewModelProvider)).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_services_ServiceDetailViewModel, ((Provider) serviceDetailViewModelProvider)).put(LazyClassKeyProvider.com_bahuchar_bikecare_staff_ui_services_ServicesViewModel, ((Provider) servicesViewModelProvider)).build());
    }

    @Override
    public Map<Class<?>, Object> getHiltViewModelAssistedMap() {
      return Collections.<Class<?>, Object>emptyMap();
    }

    @IdentifierNameString
    private static final class LazyClassKeyProvider {
      static String com_bahuchar_bikecare_staff_ui_bills_BillDetailViewModel = "com.bahuchar.bikecare.staff.ui.bills.BillDetailViewModel";

      static String com_bahuchar_bikecare_staff_ui_services_ServiceDetailViewModel = "com.bahuchar.bikecare.staff.ui.services.ServiceDetailViewModel";

      static String com_bahuchar_bikecare_staff_ui_bills_BillsViewModel = "com.bahuchar.bikecare.staff.ui.bills.BillsViewModel";

      static String com_bahuchar_bikecare_staff_ui_customers_CustomerDetailViewModel = "com.bahuchar.bikecare.staff.ui.customers.CustomerDetailViewModel";

      static String com_bahuchar_bikecare_staff_ui_customers_CustomersViewModel = "com.bahuchar.bikecare.staff.ui.customers.CustomersViewModel";

      static String com_bahuchar_bikecare_staff_ui_auth_AuthViewModel = "com.bahuchar.bikecare.staff.ui.auth.AuthViewModel";

      static String com_bahuchar_bikecare_staff_ui_services_CreateServiceViewModel = "com.bahuchar.bikecare.staff.ui.services.CreateServiceViewModel";

      static String com_bahuchar_bikecare_staff_ui_dashboard_DashboardViewModel = "com.bahuchar.bikecare.staff.ui.dashboard.DashboardViewModel";

      static String com_bahuchar_bikecare_staff_ui_customers_CreateCustomerViewModel = "com.bahuchar.bikecare.staff.ui.customers.CreateCustomerViewModel";

      static String com_bahuchar_bikecare_staff_ui_services_ServicesViewModel = "com.bahuchar.bikecare.staff.ui.services.ServicesViewModel";

      @KeepFieldType
      BillDetailViewModel com_bahuchar_bikecare_staff_ui_bills_BillDetailViewModel2;

      @KeepFieldType
      ServiceDetailViewModel com_bahuchar_bikecare_staff_ui_services_ServiceDetailViewModel2;

      @KeepFieldType
      BillsViewModel com_bahuchar_bikecare_staff_ui_bills_BillsViewModel2;

      @KeepFieldType
      CustomerDetailViewModel com_bahuchar_bikecare_staff_ui_customers_CustomerDetailViewModel2;

      @KeepFieldType
      CustomersViewModel com_bahuchar_bikecare_staff_ui_customers_CustomersViewModel2;

      @KeepFieldType
      AuthViewModel com_bahuchar_bikecare_staff_ui_auth_AuthViewModel2;

      @KeepFieldType
      CreateServiceViewModel com_bahuchar_bikecare_staff_ui_services_CreateServiceViewModel2;

      @KeepFieldType
      DashboardViewModel com_bahuchar_bikecare_staff_ui_dashboard_DashboardViewModel2;

      @KeepFieldType
      CreateCustomerViewModel com_bahuchar_bikecare_staff_ui_customers_CreateCustomerViewModel2;

      @KeepFieldType
      ServicesViewModel com_bahuchar_bikecare_staff_ui_services_ServicesViewModel2;
    }

    private static final class SwitchingProvider<T> implements Provider<T> {
      private final SingletonCImpl singletonCImpl;

      private final ActivityRetainedCImpl activityRetainedCImpl;

      private final ViewModelCImpl viewModelCImpl;

      private final int id;

      SwitchingProvider(SingletonCImpl singletonCImpl, ActivityRetainedCImpl activityRetainedCImpl,
          ViewModelCImpl viewModelCImpl, int id) {
        this.singletonCImpl = singletonCImpl;
        this.activityRetainedCImpl = activityRetainedCImpl;
        this.viewModelCImpl = viewModelCImpl;
        this.id = id;
      }

      @SuppressWarnings("unchecked")
      @Override
      public T get() {
        switch (id) {
          case 0: // com.bahuchar.bikecare.staff.ui.auth.AuthViewModel 
          return (T) new AuthViewModel(singletonCImpl.staffRepositoryProvider.get(), singletonCImpl.tokenStoreProvider.get());

          case 1: // com.bahuchar.bikecare.staff.ui.bills.BillDetailViewModel 
          return (T) new BillDetailViewModel(singletonCImpl.staffRepositoryProvider.get());

          case 2: // com.bahuchar.bikecare.staff.ui.bills.BillsViewModel 
          return (T) new BillsViewModel(singletonCImpl.staffRepositoryProvider.get());

          case 3: // com.bahuchar.bikecare.staff.ui.customers.CreateCustomerViewModel 
          return (T) new CreateCustomerViewModel(singletonCImpl.staffRepositoryProvider.get());

          case 4: // com.bahuchar.bikecare.staff.ui.services.CreateServiceViewModel 
          return (T) new CreateServiceViewModel(singletonCImpl.staffRepositoryProvider.get());

          case 5: // com.bahuchar.bikecare.staff.ui.customers.CustomerDetailViewModel 
          return (T) new CustomerDetailViewModel(singletonCImpl.staffRepositoryProvider.get());

          case 6: // com.bahuchar.bikecare.staff.ui.customers.CustomersViewModel 
          return (T) new CustomersViewModel(singletonCImpl.staffRepositoryProvider.get());

          case 7: // com.bahuchar.bikecare.staff.ui.dashboard.DashboardViewModel 
          return (T) new DashboardViewModel(singletonCImpl.staffRepositoryProvider.get());

          case 8: // com.bahuchar.bikecare.staff.ui.services.ServiceDetailViewModel 
          return (T) new ServiceDetailViewModel(singletonCImpl.staffRepositoryProvider.get());

          case 9: // com.bahuchar.bikecare.staff.ui.services.ServicesViewModel 
          return (T) new ServicesViewModel(singletonCImpl.staffRepositoryProvider.get());

          default: throw new AssertionError(id);
        }
      }
    }
  }

  private static final class ActivityRetainedCImpl extends StaffApp_HiltComponents.ActivityRetainedC {
    private final SingletonCImpl singletonCImpl;

    private final ActivityRetainedCImpl activityRetainedCImpl = this;

    private Provider<ActivityRetainedLifecycle> provideActivityRetainedLifecycleProvider;

    private ActivityRetainedCImpl(SingletonCImpl singletonCImpl,
        SavedStateHandleHolder savedStateHandleHolderParam) {
      this.singletonCImpl = singletonCImpl;

      initialize(savedStateHandleHolderParam);

    }

    @SuppressWarnings("unchecked")
    private void initialize(final SavedStateHandleHolder savedStateHandleHolderParam) {
      this.provideActivityRetainedLifecycleProvider = DoubleCheck.provider(new SwitchingProvider<ActivityRetainedLifecycle>(singletonCImpl, activityRetainedCImpl, 0));
    }

    @Override
    public ActivityComponentBuilder activityComponentBuilder() {
      return new ActivityCBuilder(singletonCImpl, activityRetainedCImpl);
    }

    @Override
    public ActivityRetainedLifecycle getActivityRetainedLifecycle() {
      return provideActivityRetainedLifecycleProvider.get();
    }

    private static final class SwitchingProvider<T> implements Provider<T> {
      private final SingletonCImpl singletonCImpl;

      private final ActivityRetainedCImpl activityRetainedCImpl;

      private final int id;

      SwitchingProvider(SingletonCImpl singletonCImpl, ActivityRetainedCImpl activityRetainedCImpl,
          int id) {
        this.singletonCImpl = singletonCImpl;
        this.activityRetainedCImpl = activityRetainedCImpl;
        this.id = id;
      }

      @SuppressWarnings("unchecked")
      @Override
      public T get() {
        switch (id) {
          case 0: // dagger.hilt.android.ActivityRetainedLifecycle 
          return (T) ActivityRetainedComponentManager_LifecycleModule_ProvideActivityRetainedLifecycleFactory.provideActivityRetainedLifecycle();

          default: throw new AssertionError(id);
        }
      }
    }
  }

  private static final class ServiceCImpl extends StaffApp_HiltComponents.ServiceC {
    private final SingletonCImpl singletonCImpl;

    private final ServiceCImpl serviceCImpl = this;

    private ServiceCImpl(SingletonCImpl singletonCImpl, Service serviceParam) {
      this.singletonCImpl = singletonCImpl;


    }
  }

  private static final class SingletonCImpl extends StaffApp_HiltComponents.SingletonC {
    private final ApplicationContextModule applicationContextModule;

    private final SingletonCImpl singletonCImpl = this;

    private Provider<TokenStore> tokenStoreProvider;

    private Provider<AuthInterceptor> authInterceptorProvider;

    private Provider<UnauthorizedInterceptor> unauthorizedInterceptorProvider;

    private Provider<ApiClient> apiClientProvider;

    private Provider<NetworkConfig> provideNetworkConfigProvider;

    private Provider<StaffApi> provideStaffApiProvider;

    private Provider<StaffRepository> staffRepositoryProvider;

    private SingletonCImpl(ApplicationContextModule applicationContextModuleParam) {
      this.applicationContextModule = applicationContextModuleParam;
      initialize(applicationContextModuleParam);

    }

    @SuppressWarnings("unchecked")
    private void initialize(final ApplicationContextModule applicationContextModuleParam) {
      this.tokenStoreProvider = DoubleCheck.provider(new SwitchingProvider<TokenStore>(singletonCImpl, 4));
      this.authInterceptorProvider = DoubleCheck.provider(new SwitchingProvider<AuthInterceptor>(singletonCImpl, 3));
      this.unauthorizedInterceptorProvider = DoubleCheck.provider(new SwitchingProvider<UnauthorizedInterceptor>(singletonCImpl, 5));
      this.apiClientProvider = DoubleCheck.provider(new SwitchingProvider<ApiClient>(singletonCImpl, 2));
      this.provideNetworkConfigProvider = DoubleCheck.provider(new SwitchingProvider<NetworkConfig>(singletonCImpl, 6));
      this.provideStaffApiProvider = DoubleCheck.provider(new SwitchingProvider<StaffApi>(singletonCImpl, 1));
      this.staffRepositoryProvider = DoubleCheck.provider(new SwitchingProvider<StaffRepository>(singletonCImpl, 0));
    }

    @Override
    public void injectStaffApp(StaffApp arg0) {
    }

    @Override
    public Set<Boolean> getDisableFragmentGetContextFix() {
      return Collections.<Boolean>emptySet();
    }

    @Override
    public ActivityRetainedComponentBuilder retainedComponentBuilder() {
      return new ActivityRetainedCBuilder(singletonCImpl);
    }

    @Override
    public ServiceComponentBuilder serviceComponentBuilder() {
      return new ServiceCBuilder(singletonCImpl);
    }

    private static final class SwitchingProvider<T> implements Provider<T> {
      private final SingletonCImpl singletonCImpl;

      private final int id;

      SwitchingProvider(SingletonCImpl singletonCImpl, int id) {
        this.singletonCImpl = singletonCImpl;
        this.id = id;
      }

      @SuppressWarnings("unchecked")
      @Override
      public T get() {
        switch (id) {
          case 0: // com.bahuchar.bikecare.staff.data.StaffRepository 
          return (T) new StaffRepository(singletonCImpl.provideStaffApiProvider.get(), singletonCImpl.tokenStoreProvider.get());

          case 1: // com.bahuchar.bikecare.core.data.api.StaffApi 
          return (T) StaffNetworkModule_ProvideStaffApiFactory.provideStaffApi(singletonCImpl.apiClientProvider.get(), singletonCImpl.provideNetworkConfigProvider.get());

          case 2: // com.bahuchar.bikecare.core.data.network.ApiClient 
          return (T) new ApiClient(singletonCImpl.authInterceptorProvider.get(), singletonCImpl.unauthorizedInterceptorProvider.get());

          case 3: // com.bahuchar.bikecare.core.data.network.AuthInterceptor 
          return (T) new AuthInterceptor(singletonCImpl.tokenStoreProvider.get());

          case 4: // com.bahuchar.bikecare.core.data.local.TokenStore 
          return (T) new TokenStore(ApplicationContextModule_ProvideContextFactory.provideContext(singletonCImpl.applicationContextModule));

          case 5: // com.bahuchar.bikecare.core.data.network.UnauthorizedInterceptor 
          return (T) new UnauthorizedInterceptor(singletonCImpl.tokenStoreProvider.get());

          case 6: // com.bahuchar.bikecare.core.data.network.NetworkConfig 
          return (T) StaffNetworkModule_ProvideNetworkConfigFactory.provideNetworkConfig();

          default: throw new AssertionError(id);
        }
      }
    }
  }
}
